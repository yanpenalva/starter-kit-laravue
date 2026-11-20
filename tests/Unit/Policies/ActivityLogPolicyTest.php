<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;

describe('ActivityLogPolicy', function () {
    beforeEach(function () {
        $this->policy = new ActivityLogPolicy();
        app('cache')->store()->put(config('permission.cache.key'), [
            'alias' => [],
            'permissions' => [],
            'roles' => [],
        ]);
    });

    it('allows users with activity logs list permission to view index', function () {
        Gate::define(PermissionEnum::ACTIVITY_LOGS_LIST->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without activity logs list permission to view index', function () {
        Gate::define(PermissionEnum::ACTIVITY_LOGS_LIST->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('verifies ActivityLogPolicy class structure', function () {
        $reflection = new ReflectionClass(ActivityLogPolicy::class);

        expect($reflection->hasMethod('index'))->toBeTrue();

        $params = $reflection->getMethod('index')->getParameters();
        expect($params[0]->getType()->getName())->toBe(User::class);
    });
})->group('policies', 'unit');
