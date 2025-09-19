<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\{LoginAction, LogoutAction, MyProfileAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @apiInfo
 *
 * @title SP 2.0
 *
 * @version 1.0.0
 *
 * @description staterkit
 */
final class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            /** @var \Illuminate\Support\Fluent<string, string> $params */
            $params = $request->fluentParams();

            $data = app(LoginAction::class)->execute($params);

            return response()->json($data);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        }
    }

    public function logout(): JsonResponse
    {
        $data = app(LogoutAction::class)->execute();

        return response()->json($data);
    }

    public function myProfile(): JsonResponse
    {
        $data = app(MyProfileAction::class)->execute();

        return response()->json($data);
    }
}
