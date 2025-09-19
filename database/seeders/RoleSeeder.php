<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['slug' => RolesEnum::ADMINISTRATOR->value],
            [
                'name' => RolesEnum::ADMINISTRATOR->label(),
                'guard_name' => 'web',
                'slug' => RolesEnum::ADMINISTRATOR->value,
                'description' => 'Administrador da plataforma com acesso completo.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $permissions = Permission::all()->pluck('id')->toArray();
        $adminRole->syncPermissions($permissions);

        Role::updateOrCreate(
            ['slug' => RolesEnum::GUEST->value],
            [
                'name' => RolesEnum::GUEST->label(),
                'guard_name' => 'web',
                'slug' => RolesEnum::GUEST->value,
                'description' => 'Visitante na plataforma com acesso limitado.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
