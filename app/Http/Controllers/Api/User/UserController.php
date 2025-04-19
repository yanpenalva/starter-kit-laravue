<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\User;

use App\Actions\User\{CreateExternalUserAction, CreateUserAction, DeleteUserAction, ListUserAction, ShowUserAction, UpdateUserAction, VerifyAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\User\{CreateUserRequest, IndexUserRequest, RegisterExternalUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    public function index(IndexUserRequest $request): JsonResource
    {
        $this->authorize('index', User::class);

        $users = app(ListUserAction::class)->execute($request->fluent());

        return new UserResource($users);
    }

    public function store(CreateUserRequest $request): JsonResource
    {
        $this->authorize('create', User::class);

        $user = app(CreateUserAction::class)->execute($request->fluent());

        return new UserResource($user);
    }

    public function show(int $id): JsonResource
    {
        $this->authorize('show', User::class);

        $showUser = app(ShowUserAction::class)->execute($id);

        return new UserResource($showUser);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResource
    {
        $this->authorize('update', User::class);

        $updatedUser = app(UpdateUserAction::class)->execute(
            params: $request->fluent(),
            id: (int) $id
        );

        return new UserResource($updatedUser);
    }

    public function destroy(Request $request, User $user): Response
    {
        $this->authorize('delete', User::class);

        try {
            $response = app(DeleteUserAction::class)->execute(params: $request->fluent(), user: $user);

            if (!$response) {
                return response()->json([
                    'message' => 'Erro ao deletar usuário. Ação não concluída.',
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'message' => 'Usuário deletado com sucesso!',
            ], Response::HTTP_NO_CONTENT);

        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Erro ao deletar usuário.',
                'error' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function register(RegisterExternalUserRequest $request): JsonResponse
    {
        $user = app(CreateExternalUserAction::class)->execute($request->fluent());

        return response()->json([
            'message' => "Um e-mail de confirmação foi encaminhado para {$user->email}. Por favor, realize os procedimentos para ativação da sua conta.",
        ], Response::HTTP_OK);
    }

    public function verify(Request $request): JsonResponse
    {
        try {
            app(VerifyAction::class)->execute($request->fluent());

            return response()->json([
                'message' => 'O seu cadastro foi verificado com sucesso!',
            ], Response::HTTP_OK);
        } catch (Exception $exeption) {
            return response()->json([
                'message' => $exeption->getMessage(),
            ], $exeption->getCode());
        }
    }
}
