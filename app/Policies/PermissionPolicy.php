<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;

final class PermissionPolicy {
    public function index(User $user): bool {
        return $user->can(PermissionEnum::ROLES_LIST->value);
    }
}
