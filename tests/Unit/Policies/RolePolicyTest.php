<?php

declare(strict_types = 1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\RolePolicy;
use Mockery;
use ReflectionClass;
use Spatie\Permission\Models\Role;

describe('RolePolicy', function () {
    beforeEach(function () {
        $this->policy = new RolePolicy;
    });

    it('allows users with roles.list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('roles.list')
            ->once()
            ->andReturn(true);

        $result = $this->policy->index($user);

        expect($result)->toBeTrue();
    });

    it('denies users without roles.list permission to view index', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('roles.list')
            ->once()
            ->andReturn(false);

        $result = $this->policy->index($user);

        expect($result)->toBeFalse();
    });

    it('allows users with roles.list permission to list all roles', function () {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with('roles.list')
            ->once()
            ->andReturn(true);

        $result = $this->policy->listAll($user);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.view permission to show a role', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.view')
            ->once()
            ->andReturn(true);

        $result = $this->policy->show($user, $role);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.create permission to store a role', function () {
        $user = Mockery::mock(User::class);

        $user->shouldReceive('can')
            ->with('roles.create')
            ->once()
            ->andReturn(true);

        $result = $this->policy->store($user);

        expect($result)->toBeTrue();
    });

    it('allows users with roles.edit permission to update a non-admin role', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.edit')
            ->once()
            ->andReturn(true);

        // Configurar o mock para retornar id = 2
        $role->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(2);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeTrue();
    });

    it('denies users to update the admin role (id=1)', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.edit')
            ->once()
            ->andReturn(true);

        // Configurar o mock para retornar id = 1
        $role->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeFalse();
    });

    it('denies users without roles.edit permission to update any role', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.edit')
            ->once()
            ->andReturn(false);

        // Configurar o mock para retornar id = 2
        $role->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(2);

        $result = $this->policy->update($user, $role);

        expect($result)->toBeFalse();
    });

    it('allows users with roles.delete permission to delete a role', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.delete')
            ->once()
            ->andReturn(true);

        $result = $this->policy->delete($user, $role);

        expect($result)->toBeTrue();
    });

    it('denies users without roles.delete permission to delete a role', function () {
        $user = Mockery::mock(User::class);
        $role = Mockery::mock(Role::class);

        $user->shouldReceive('can')
            ->with('roles.delete')
            ->once()
            ->andReturn(false);

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
