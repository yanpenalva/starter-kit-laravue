<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            PermissionEnum::USERS_LIST,
            PermissionEnum::USERS_CREATE,
            PermissionEnum::USERS_UPDATE,
            PermissionEnum::USERS_DELETE,
            PermissionEnum::USERS_SHOW,
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
