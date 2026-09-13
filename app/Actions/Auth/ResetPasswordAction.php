<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResetPasswordAction
{
    /**
     * @param  Fluent<string, mixed>  $params
     */
    public function execute(Fluent $params): void
    {
        $status = Password::reset(
            $params->toArray(),
            function (User $user, string $password): void {
                $user->update(['password' => $password]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            abort(
                Response::HTTP_BAD_REQUEST,
                'Não foi possível realizar a troca de senha, por favor tente novamente mais tarde.'
            );
        }
    }
}
