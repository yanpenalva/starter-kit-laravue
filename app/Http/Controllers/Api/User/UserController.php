<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\User;

use App\Actions\User\{CreateExternalUserAction, CreateUserAction, DeleteUserAction, ListUserAction, ShowUserAction, UpdateUserAction, VerifyAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\User\{CreateUserRequest, IndexUserRequest, RegisterExternalUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UserController extends Controller
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
        $showUser = app(ShowUserAction::class)->execute($id);

        $this->authorize('show', $showUser);

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
            app(DeleteUserAction::class)->execute(
                params: $request->fluent(),
                user: $user
            );

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
        app(VerifyAction::class)->execute($request->fluent());

        return response()->json(
            ['message' => 'O seu cadastro foi verificado com sucesso!'],
            Response::HTTP_OK
        );
    }
}
