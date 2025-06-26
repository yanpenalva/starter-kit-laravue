<?php declare(strict_types = 1);

use App\Actions\Role\RoleBySlugAction;
use Spatie\Permission\Models\{Permission, Role};

describe('RoleBySlugAction (Feature)', function () {
    it('returns role by slug without permissions', function () {
        Role::create([
            'name' => 'Coordinator',
            'slug' => 'coordinator',
        ]);

        $result = app(RoleBySlugAction::class)->execute('coordinator');

        expect($result)
            ->toBeInstanceOf(Role::class)
            ->slug->toBe('coordinator')
            ->relationLoaded('permissions')->toBeFalse();
    });

    it('returns role by slug with permissions and mapped_permissions', function () {
        $role = Role::create([
            'name' => 'Coordinator',
            'slug' => 'coordinator',
        ]);

        $permission = Permission::create([
            'name' => 'view reports',
            'description' => 'View Reports',
            'resource' => 'reports',
        ]);

        $role->givePermissionTo($permission);

        $result = app(RoleBySlugAction::class)->execute('coordinator', true);

        expect($result)
            ->toBeInstanceOf(Role::class)
            ->slug->toBe('coordinator')
            ->relationLoaded('permissions')->toBeTrue()
            ->permissions->count()->toBe(1);

        expect($result->mapped_permissions[0])->toMatchArray([
            'value' => $permission->id,
            'label' => $permission->description,
        ]);
    });

    it('throws exception when role slug does not exist', function () {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        app(RoleBySlugAction::class)->execute('non-existent-slug');
    });
})->group('feature', 'role');
