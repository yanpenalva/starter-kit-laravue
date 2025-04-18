<?php

use App\Actions\User\RemoveUserRoleAction;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('RemoveUserRoleAction (Feature)', function () {
    it('removes all roles from user', function () {
        $admin = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $editor = Role::create(['name' => 'Editor', 'slug' => 'editor']);

        $user = User::factory()->create();
        $user->assignRole($admin, $editor);

        expect($user->getRoleNames())->toHaveCount(2);

        app(RemoveUserRoleAction::class)->execute($user);

        expect($user->fresh()->getRoleNames())->toBeEmpty();
    });

    it('does not fail when user has no roles', function () {
        $user = User::factory()->create();

        expect($user->getRoleNames())->toBeEmpty();

        app(RemoveUserRoleAction::class)->execute($user);

        expect($user->fresh()->getRoleNames())->toBeEmpty();
    });
})->group('feature', 'user');
