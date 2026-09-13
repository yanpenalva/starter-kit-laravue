<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Mail\SendForgetPasswordMail;
use App\Models\User;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\{DB, Mail, Password};
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;

final readonly class ForgotPasswordAction
{
    /**
     * @param  Fluent<string,mixed>  $params
     */
    public function execute(Fluent $params): void
    {
        DB::transaction(fn () => $this->process($params));
    }

    /**
     * @param  Fluent<string,mixed>  $params
     */
    private function process(Fluent $params): void
    {
        $status = Password::sendResetLink(
            $params->toArray(),
            fn (CanResetPassword $passwordResettableUser, string $resetToken) => $this->queueResetEmail($passwordResettableUser, $resetToken)
        );

        $isSuccessful = $status === Password::RESET_LINK_SENT || is_string($status);

        if (!$isSuccessful) {
            abort(
                Response::HTTP_BAD_REQUEST,
                'Não foi possível realizar a solicitação de redefinição de senha, verifique se os dados informados são válidos.'
            );
        }
    }

    /**
     * @param CanResetPassword $passwordResettableUser
     */
    private function queueResetEmail(CanResetPassword $passwordResettableUser, string $resetToken): void
    {
        /** @var User $user */
        $user = User::findOrFail($passwordResettableUser->getAuthIdentifier());

        Mail::to($user)->queue(
            new SendForgetPasswordMail(
                token: $resetToken,
                user: $user
            )
        );
    }
}
