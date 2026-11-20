<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Enums\{PermissionEnum, RolesEnum};
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use Spatie\Permission\Models\Role;

describe('UserPolicy', function () {
    beforeEach(function () {
        $this->policy = new UserPolicy();
        app('cache')->store()->put(config('permission.cache.key'), [
            'alias' => [],
            'permissions' => [],
            'roles' => [],
        ]);
    });

    it('allows users with users.list permission to view index', function () {
        Gate::define(PermissionEnum::USERS_LIST->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.list permission to view index', function () {
        Gate::define(PermissionEnum::USERS_LIST->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('allows users with users.create permission to create', function () {
        Gate::define(PermissionEnum::USERS_CREATE->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->create($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.create permission to create', function () {
        Gate::define(PermissionEnum::USERS_CREATE->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->create($user);

        expect($result)->toBeFalse();
    });

    it('allows admin to view any user via show', function () {
        $actor = new User();
        $actor->id = 1;
        $role = new Role(['slug' => RolesEnum::ADMINISTRATOR->value]);
        $actor->setRelation('roles', collect([$role]));

        $target = new User();
        $target->id = 2;

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeTrue();
    });

    it('allows owner with users.show permission to view own profile', function () {
        $actor = new User();
        $actor->id = 10;
        $role = new Role(['slug' => RolesEnum::GUEST->value]);
        $actor->setRelation('roles', collect([$role]));

        Gate::define(PermissionEnum::USERS_SHOW->value, fn (User $user): bool => true);

        $target = new User();
        $target->id = 10;

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeTrue();
    });

    it('denies non-owner without users.show permission to view other profile', function () {
        $actor = new User();
        $actor->id = 11;
        $role = new Role(['slug' => RolesEnum::GUEST->value]);
        $actor->setRelation('roles', collect([$role]));

        Gate::define(PermissionEnum::USERS_SHOW->value, fn (User $user): bool => false);

        $target = new User();
        $target->id = 12;

        $result = $this->policy->show($actor, $target);

        expect($result)->toBeFalse();
    });

    it('allows users with users.update permission to update', function () {
        Gate::define(PermissionEnum::USERS_UPDATE->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->update($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.update permission to update', function () {
        Gate::define(PermissionEnum::USERS_UPDATE->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->update($user);

        expect($result)->toBeFalse();
    });

    it('allows users with users.delete permission to delete', function () {
        Gate::define(PermissionEnum::USERS_DELETE->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->delete($user);

        expect($result)->toBeTrue();
    });

    it('denies users without users.delete permission to delete', function () {
        Gate::define(PermissionEnum::USERS_DELETE->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->delete($user);

        expect($result)->toBeFalse();
    });

    it('checks if policy class exists and is properly defined', function () {
        $reflection = new ReflectionClass(UserPolicy::class);

        expect($reflection->hasMethod('index'))->toBeTrue();
        expect($reflection->getMethod('index')->getParameters()[0]->getType()->getName())->toBe(User::class);
    });
})->group('policies');
