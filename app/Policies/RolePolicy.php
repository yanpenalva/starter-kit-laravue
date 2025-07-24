<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy {
    public function index(User $user): bool {
        return $user->can('roles.list');
    }

    public function listAll(User $user): bool {
        return $user->can('roles.list');
    }

    public function show(User $user, Role $role): bool {
        return $user->can('roles.view');
    }

    public function store(User $user): bool {
        return $user->can('roles.create');
    }
    public function update(User $user, Role $role): bool {
        return $user->can('roles.edit') && $role->id !== 1;
    }

    public function delete(User $user, Role $role): bool {
        return $user->can('roles.delete');
    }
}
