<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;

final class ActivityLogPolicy {
    public function index(User $user): bool {
        return $user->can(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    }
}
