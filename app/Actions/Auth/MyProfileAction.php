<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

final readonly class MyProfileAction
{
    /** @return array<string, mixed> */
    public function execute(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        $user->loadMissing(['roles', 'roles.permissions']);

        /** @var Collection<int, Role> $roles */
        $roles = $user->roles;

        return [
            ...$user->only(['name', 'email']),

            'permissions' => $user->getAllPermissions()
                ->values()
                ->toArray(),

            'roles' => $roles
                ->map(
                    /** @param Role $role */
                    static fn (Role $role): array => [
                        'id' => $role->getKey(),
                        'name' => $role->name,
                        'slug' => $role->getAttribute('slug'),
                    ]
                )
                ->values()
                ->toArray(),
        ];
    }
}
