<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

final class ActivityLogPolicy
{
    public function index(User $user): bool
    {
        return $user->can(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->can(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    }
}
