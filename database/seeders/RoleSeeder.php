<?php declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['slug' => 'administrator'],
            [
                'name' => 'Administrador',
                'guard_name' => 'web',
                'slug' => 'administrator',
                'description' => 'Administrador da plataforma com acesso completo.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $permissions = Permission::all()->pluck('id')->toArray();
        $adminRole->syncPermissions($permissions);

        Role::updateOrCreate(
            ['slug' => 'guest'],
            [
                'name' => 'Visitante',
                'guard_name' => 'web',
                'slug' => 'guest',
                'description' => 'Visitante na plataforma com acesso limitado.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

    }
}
