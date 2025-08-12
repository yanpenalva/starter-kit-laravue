<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\{Permission, Role};

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

        /** @var \Illuminate\Database\Eloquent\Collection<int, Role> $roles */
        $roles = $user->roles()->get();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions */
        $permissions = $user->getAllPermissions();

        return [
            ...$user->only(['name', 'email']),

            'permissions' => $permissions->values()->toArray(),

            'roles' => $roles
                ->map(
                    static fn (Role $role, int $index): array => [
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
