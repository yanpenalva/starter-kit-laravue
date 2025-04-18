<?php

use App\Actions\User\ShowUserAction;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


describe('ShowUserAction (Feature)', function () {
    it('returns user with roles and permissions', function () {
        $permission = Permission::create([
            'name' => 'edit articles',
            'description' => 'Edit articles',
            'resource' => 'articles',
        ]);

        $role = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
        ]);

        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $result = app(ShowUserAction::class)->execute($user->getKey());

        expect($result)
            ->toBeInstanceOf(User::class)
            ->id->toBe($user->id)
            ->relationLoaded('roles')->toBeTrue()
            ->roles->first()->relationLoaded('permissions')->toBeTrue();
    });

    it('throws exception when user is not found', function () {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        app(ShowUserAction::class)->execute(9999); // ID inexistente
    });
})->group('feature', 'user');
