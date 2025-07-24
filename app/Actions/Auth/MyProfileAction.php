<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;

final readonly class MyProfileAction {
    /**
     * @return array<string, mixed>
     */
    public function execute(): array {
        /** @var User|null $user */
        $user = auth()->user();

        return $user ? [
            ...$user->only(['name', 'email']),

            'permissions' => $user->getAllPermissions()->toArray(),
            'roles' => $user->roles()->get(['id', 'name', 'slug'])->toArray(),
        ]
            : [];
    }
}
