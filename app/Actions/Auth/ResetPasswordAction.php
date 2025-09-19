<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class ResetPasswordAction
{
    /**
     * Send the password reset link to the user.
     *
     * @param  Fluent<string, mixed>  $params
     */
    public function execute(Fluent $params): void
    {
        try {
            $status = Password::reset(
                $params->toArray(),
                function (User $user, string $password): void {
                    $user->update(['password' => $password]);
                }
            );

            throw_if(
                $status !== Password::PASSWORD_RESET,
                new Exception(
                    'Não foi possível realizar a troca de senha, por favor tente novamente mais tarde.',
                    Response::HTTP_BAD_REQUEST
                )
            );
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
