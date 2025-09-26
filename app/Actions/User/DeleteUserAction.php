<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Actions\User\RemoveUserRoleAction as RemoveRole;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final readonly class DeleteUserAction {
    use LogsActivity;

    /**
     * @param  Fluent<int|string, mixed>  $params
     */
    public function execute(Fluent $params, User $user): bool {
        return DB::transaction(function () use ($user) {
            throw_if(
                auth()->id() === $user->id,
                BadRequestException::class,
                'Não é possível realizar essa ação.'
            );

            $this->logDeleteActivity('users', $user, 'Excluiu um usuário');

            app(RemoveRole::class)->execute($user);

            return (bool) $user->delete();
        });
    }
}
