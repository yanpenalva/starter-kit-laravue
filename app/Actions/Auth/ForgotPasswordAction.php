<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Enums\RolesEnum;
use App\Mail\SendForgetPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\{DB, Mail, Password};
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;

final readonly class ForgotPasswordAction
{
    /**
     * Send the password reset link to the user.
     *
     * @template TKey of array-key
     * @template TValue
     * @param  Fluent<TKey, TValue>  $params
     * @return void
     */
    public function execute(Fluent $params): void
    {
        DB::transaction(function () use ($params) {
            $status = Password::sendResetLink(
                $params->toArray(),
                /**
                 * @param User&CanResetPassword $user
                 */
                function (CanResetPassword $user, string $token): void {
                    /** @var User $user */

                    throw_if(
                        !$user->roles->where('slug', RolesEnum::GUEST->value)->count(),
                        new Exception(
                            'Usuário não disponível para solicitar a redefinição de senha.',
                            Response::HTTP_CONFLICT
                        )
                    );

                    Mail::to($user)->queue(new SendForgetPasswordMail($token, $user));
                }
            );

            throw_if(
                $status !== Password::RESET_LINK_SENT,
                new Exception(
                    'Não foi possível realizar a solicitação de redefinição de senha, verifique se os dados informados são válidos.',
                    Response::HTTP_BAD_REQUEST
                )
            );
        });
    }
}
