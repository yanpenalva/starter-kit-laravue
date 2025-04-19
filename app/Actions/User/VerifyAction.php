<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;

final readonly class VerifyAction
{
    /**
     * @param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params): void
    {
        DB::transaction(function () use ($params) {
            throw_if(
                !request()->hasValidSignature(),
                Exception::class,
                'Link de validação expirado, cadastre-se novamente',
                Response::HTTP_NOT_FOUND
            );

            // @phpstan-ignore-next-line
            $userId = $params->id;

            /** @var User $user */
            $user = User::query()->whereKey($userId)->firstOrFail();

            throw_if(
                $user->email_verified_at !== null,
                Exception::class,
                'Seu cadastro já foi validado!
                Por favor, aguarde até que um administrador
                realize a liberação do seu acesso.',
                Response::HTTP_CONFLICT
            );

            $user->update([
                'email_verified_at' => now(),
            ]);
        });
    }
}
