<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use Mockery;
use ReflectionClass;

describe('ActivityLogPolicy', function () {
    beforeEach(function () {
        $this->policy = new ActivityLogPolicy();
    });

    it('allows users with activity logs list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with(PermissionEnum::ACTIVITY_LOGS_LIST->value)
            ->once()
            ->andReturn(true);

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without activity logs list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with(PermissionEnum::ACTIVITY_LOGS_LIST->value)
            ->once()
            ->andReturn(false);

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
