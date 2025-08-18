<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserPermissionSeeder::class,
            RolePermissionSeeder::class,
            ActivityLogPermissionSeeder::class,
        ]);
    }
}
