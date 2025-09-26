<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Enums\{PermissionEnum, RolesEnum};
use App\Models\User;

final class UserPolicy {
    public function index(User $user): bool {
        return $user->can(PermissionEnum::USERS_LIST->value);
    }
    public function create(User $user): bool {
        return $user->can(PermissionEnum::USERS_CREATE->value);
    }

    public function show(User $actor, User $target): bool {
        if ($actor->roles->contains('slug', RolesEnum::ADMINISTRATOR->value)) {
            return true;
        }

        return $actor->id === $target->id && $actor->can(PermissionEnum::USERS_SHOW->value);
    }
    public function update(User $user): bool {
        return $user->can(PermissionEnum::USERS_UPDATE->value);
    }

    public function delete(User $user): bool {
        return $user->can(PermissionEnum::USERS_DELETE->value);
    }
}
