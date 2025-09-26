<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Models\User;

final readonly class RemoveUserRoleAction {
    public function execute(User $user): void {
        $user->load('roles');

        $user->getRoleNames()
            ->each(static function (mixed $role) use ($user): bool {
                assert(is_string($role));
                $user->removeRole($role);

                return true;
            });
    }
}
