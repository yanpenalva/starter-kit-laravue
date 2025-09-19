<?php
declare(strict_types = 1);

use App\Actions\Role\ListRoleAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\{Permission, Role};

uses(RefreshDatabase::class);

describe('ListRoleAction (Feature)', function () {
    beforeEach(function () {
        Role::query()->delete();
        Permission::query()->delete();

        $perm1 = Permission::create([
            'name' => 'edit-posts',
            'description' => 'Edit posts',
            'resource' => 'posts',
        ]);

        $perm2 = Permission::create([
            'name' => 'delete-posts',
            'description' => 'Delete posts',
            'resource' => 'posts',
        ]);

        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Admin role',
            'slug' => 'admin',
        ]);

        $editor = Role::create([
            'name' => 'Editor',
            'description' => 'Editor role',
            'slug' => 'editor',
        ]);

        $viewer = Role::create([
            'name' => 'Viewer',
            'description' => 'Viewer role',
            'slug' => 'viewer',
        ]);

        $admin->givePermissionTo($perm1, $perm2);
        $editor->givePermissionTo($perm1);
    });

    it('returns all roles without pagination', function () {
        $params = new Fluent([
            'paginated' => false,
        ]);

        $result = app(ListRoleAction::class)->execute($params);

        expect($result)->toHaveCount(3);
        expect($result->first()->relationLoaded('permissions'))->toBeTrue();
    });

    it('returns paginated roles', function () {
        $params = new Fluent([
            'paginated' => true,
            'limit' => 2,
        ]);

        $result = app(ListRoleAction::class)->execute($params);

        expect($result->total())->toBe(3);
        expect($result->perPage())->toBe(2);
        expect($result->items())->toHaveCount(2);
    });

    it('filters roles using search parameter', function () {
        $params = new Fluent([
            'search' => 'Admin',
            'paginated' => false,
        ]);

        $result = app(ListRoleAction::class)->execute($params);

        expect($result)->toHaveCount(1);
        expect($result->first()->name)->toBe('Admin');
    });

    it('orders roles by name descending', function () {
        $params = new Fluent([
            'order' => 'desc',
            'column' => 'name',
            'paginated' => false,
        ]);

        $result = app(ListRoleAction::class)->execute($params);

        expect($result->first()->name)->toBe('Viewer');
    });

    it('orders roles by description ascending', function () {
        $params = new Fluent([
            'order' => 'asc',
            'column' => 'description',
            'paginated' => false,
        ]);

        $result = app(ListRoleAction::class)->execute($params);

        expect($result->pluck('description')->values()->all())->toMatchArray([
            'Admin role',
            'Editor role',
            'Viewer role',
        ]);
    });
})->group('feature', 'role');
