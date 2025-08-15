<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;

final class RolePolicy
{
    public function index(User $user): bool
    {
        return $user->can(PermissionEnum::ROLES_LIST->value);
    }

    public function listAll(User $user): bool
    {
        return $user->can(PermissionEnum::ROLES_LIST->value);
    }

    public function show(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::ROLES_VIEW->value);
    }

    public function store(User $user): bool
    {
        return $user->can(PermissionEnum::ROLES_CREATE->value);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::ROLES_EDIT->value) && $role->id !== 1;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::ROLES_DELETE->value);
    }
}
