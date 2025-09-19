<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Enums\{PermissionEnum, RolesEnum};
use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;
use ReflectionClass;

describe('UserPolicy', function () {
    beforeEach(function () {
        $this->policy = new UserPolicy;
    });

    afterEach(function () {
        Mockery::close();
    });

    it('allows users with users.list permission to view index', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_LIST->value)
            ->once()
            ->andReturn(true);

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.list permission to view index', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_LIST->value)
            ->once()
            ->andReturn(false);

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('allows users with users.create permission to create', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_CREATE->value)
            ->once()
            ->andReturn(true);

        $result = $this->policy->create($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.create permission to create', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_CREATE->value)
            ->once()
            ->andReturn(false);

        $result = $this->policy->create($user);

        expect($result)->toBeFalse();
    });

    it('allows admin to view any user via show', function () {
        $roles = Mockery::mock();
        $roles->shouldReceive('contains')->with('slug', RolesEnum::ADMINISTRATOR->value)->andReturn(true);

        $actor = Mockery::mock(User::class)->shouldIgnoreMissing();
        $actor->shouldReceive('getAttribute')->with('roles')->andReturn($roles);

        $target = Mockery::mock(User::class)->shouldIgnoreMissing();

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeTrue();
    });

    it('allows owner with users.show permission to view own profile', function () {
        $roles = Mockery::mock();
        $roles->shouldReceive('contains')->with('slug', RolesEnum::ADMINISTRATOR->value)->andReturn(false);

        $actor = Mockery::mock(User::class)->shouldIgnoreMissing();
        $actor->shouldReceive('getAttribute')->with('roles')->andReturn($roles);
        $actor->shouldReceive('getAttribute')->with('id')->andReturn(10);
        $actor->shouldReceive('can')->with(PermissionEnum::USERS_SHOW->value)->andReturn(true);

        $target = Mockery::mock(User::class)->shouldIgnoreMissing();
        $target->shouldReceive('getAttribute')->with('id')->andReturn(10);

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeTrue();
    });

    it('denies non-owner without users.show permission to view other profile', function () {
        $roles = Mockery::mock();
        $roles->shouldReceive('contains')->with('slug', RolesEnum::ADMINISTRATOR->value)->andReturn(false);

        $actor = Mockery::mock(User::class)->shouldIgnoreMissing();
        $actor->shouldReceive('getAttribute')->with('roles')->andReturn($roles);
        $actor->shouldReceive('getAttribute')->with('id')->andReturn(11);
        $actor->shouldReceive('can')->with(PermissionEnum::USERS_SHOW->value)->andReturn(false);

        $target = Mockery::mock(User::class)->shouldIgnoreMissing();
        $target->shouldReceive('getAttribute')->with('id')->andReturn(12);

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeFalse();
    });

    it('allows users with users.update permission to update', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_UPDATE->value)
            ->once()
            ->andReturn(true);

        $result = $this->policy->update($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.update permission to update', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_UPDATE->value)
            ->once()
            ->andReturn(false);

        $result = $this->policy->update($user);

        expect($result)->toBeFalse();
    });

    it('allows users with users.delete permission to delete', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_DELETE->value)
            ->once()
            ->andReturn(true);

        $result = $this->policy->delete($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.delete permission to delete', function () {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('can')
            ->with(PermissionEnum::USERS_DELETE->value)
            ->once()
            ->andReturn(false);

        $result = $this->policy->delete($user);

        expect($result)->toBeFalse();
    });

    it('checks if policy class exists and is properly defined', function () {
        $reflection = new ReflectionClass(UserPolicy::class);

        expect($reflection->hasMethod('index'))->toBeTrue();
        expect($reflection->getMethod('index')->getParameters()[0]->getType()->getName())->toBe(User::class);
    });
})->group('policies');
