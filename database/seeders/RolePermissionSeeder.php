<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class RolePermissionSeeder extends Seeder {
    public function run(): void {
        $permissions = [
            PermissionEnum::ROLES_LIST,
            PermissionEnum::ROLES_VIEW,
            PermissionEnum::ROLES_CREATE,
            PermissionEnum::ROLES_EDIT,
            PermissionEnum::ROLES_DELETE,
        ];

        DB::table('permissions')->upsert(
            collect($permissions)->map(fn (PermissionEnum $perm) => [
                'name' => $perm->value,
                'guard_name' => 'web',
                'description' => $perm->description(),
                'resource' => $perm->resource(),
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray(),
            ['name']
        );
    }
}
