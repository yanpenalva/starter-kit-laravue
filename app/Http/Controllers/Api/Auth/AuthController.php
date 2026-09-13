<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\{LoginAction, LogoutAction, MyProfileAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        /** @var \Illuminate\Support\Fluent<string, string> $params */
        $params = $request->fluentParams();

        $data = app(LoginAction::class)->execute($params);

        return response()->json($data);
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
