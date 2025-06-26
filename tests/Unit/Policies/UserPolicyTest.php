<?php declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;
use ReflectionClass;

describe('UserPolicy', function () {
    beforeEach(function () {
        $this->policy = new UserPolicy();
    });

    it('allows users with users.list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('users.list')
            ->once()
            ->andReturn(true);

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('users.list')
            ->once()
            ->andReturn(false);

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('checks if policy method calls user->can with correct permission', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('users.list')
            ->once()
            ->andReturn(true);

        $this->policy->index($user);

        expect(true)->toBeTrue();
    });

    it('verifies policy class exists and is properly defined', function () {
        $reflection = new ReflectionClass(UserPolicy::class);

        expect($reflection->hasMethod('index'))->toBeTrue();
        expect($reflection->getMethod('index')->getParameters()[0]->getType()->getName())->toBe(User::class);
    });
})->group('policies');
