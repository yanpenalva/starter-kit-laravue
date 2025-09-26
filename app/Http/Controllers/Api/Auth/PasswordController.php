<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\{ForgotPasswordAction, ResetPasswordAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{ForgotPasswordRequest, ResetPasswordRequest};
use Illuminate\Http\{JsonResponse, Response};

final class PasswordController extends Controller {
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse {
        app(ForgotPasswordAction::class)->execute($request->fluent());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse {
        app(ResetPasswordAction::class)->execute($request->fluent());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
