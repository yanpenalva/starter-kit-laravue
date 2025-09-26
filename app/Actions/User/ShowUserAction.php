<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final readonly class ShowUserAction {
    public function execute(mixed $id): User {
        $user = User::with(['roles.permissions'])->whereKey($id)->first();

        throw_if(
            !$user,
            ModelNotFoundException::class,
            'Usuário não encontrado',
            Response::HTTP_NOT_FOUND
        );

        return $user;
    }
}
