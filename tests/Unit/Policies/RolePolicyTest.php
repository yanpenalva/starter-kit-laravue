<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Enums\{PermissionEnum, RolesEnum};
use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use Spatie\Permission\Models\Role;

describe('RolePolicy', function () {
    beforeEach(function () {
        $this->policy = new RolePolicy();
        app('cache')->store()->put(config('permission.cache.key'), [
            'alias' => [],
            'permissions' => [],
            'roles' => [],
        ]);
    });

    it('allows users with roles.list permission to view index', function () {
        Gate::define(PermissionEnum::ROLES_LIST->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without roles.list permission to view index', function () {
        Gate::define(PermissionEnum::ROLES_LIST->value, fn (User $user): bool => false);
        $user = new User();

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('allows users with roles.list permission to list all roles', function () {
        Gate::define(PermissionEnum::ROLES_LIST->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->listAll($user);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.view permission to show a role', function () {
        Gate::define(PermissionEnum::ROLES_VIEW->value, fn (User $user): bool => true);
        $user = new User();
        $role = new Role();

        $result = $this->policy->show($user, $role);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.create permission to store a role', function () {
        Gate::define(PermissionEnum::ROLES_CREATE->value, fn (User $user): bool => true);
        $user = new User();

        $result = $this->policy->store($user);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.edit permission to update a non-admin role', function () {
        Gate::define(PermissionEnum::ROLES_EDIT->value, fn (User $user): bool => true);
        $user = new User();
        $role = new Role(['slug' => RolesEnum::GUEST->value]);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeTrue();
    });

    it('denies users to update the administrator role', function () {
        Gate::define(PermissionEnum::ROLES_EDIT->value, fn (User $user): bool => true);
        $user = new User();
        $role = new Role(['slug' => RolesEnum::ADMINISTRATOR->value]);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeFalse();
    });

    it('denies users without roles.edit permission to update any role', function () {
        Gate::define(PermissionEnum::ROLES_EDIT->value, fn (User $user): bool => false);
        $user = new User();
        $role = new Role(['slug' => RolesEnum::GUEST->value]);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeFalse();
    });

    it('allows users with roles.delete permission to delete a role', function () {
        Gate::define(PermissionEnum::ROLES_DELETE->value, fn (User $user): bool => true);
        $user = new User();
        $role = new Role(['slug' => RolesEnum::GUEST->value]);

        $result = $this->policy->delete($user, $role);

        expect($result)->toBeTrue();
    });

    it('denies users without roles.delete permission to delete a role', function () {
        Gate::define(PermissionEnum::ROLES_DELETE->value, fn (User $user): bool => false);
        $user = new User();
        $role = new Role(['slug' => RolesEnum::GUEST->value]);

        $result = $this->policy->delete($user, $role);

        expect($result)->toBeFalse();
    });

    it('verifies policy class exists and is properly defined', function () {
        $reflection = new ReflectionClass(RolePolicy::class);

        $methods = ['index', 'listAll', 'show', 'store', 'update', 'delete'];

        foreach ($methods as $method) {
            expect($reflection->hasMethod($method))->toBeTrue();
        }

        expect($reflection->getMethod('index')->getParameters()[0]->getType()->getName())->toBe(User::class);
        expect($reflection->getMethod('show')->getParameters()[1]->getType()->getName())->toBe(Role::class);
    });
})->group('policies');
