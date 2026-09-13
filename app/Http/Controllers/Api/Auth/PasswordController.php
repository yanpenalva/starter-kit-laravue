<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\{ForgotPasswordAction, ResetPasswordAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{ForgotPasswordRequest, ResetPasswordRequest};
use Illuminate\Http\Response;

final class PasswordController extends Controller {
    public function forgotPassword(ForgotPasswordRequest $request): Response {
        app(ForgotPasswordAction::class)->execute($request->fluent());

        return response()->noContent();
    }

    public function resetPassword(ResetPasswordRequest $request): Response {
        app(ResetPasswordAction::class)->execute($request->fluent());

        return response()->noContent();
    }
}
