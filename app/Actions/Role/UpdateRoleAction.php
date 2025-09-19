<?php

declare(strict_types = 1);

namespace App\Actions\Role;

use App\Traits\LogsActivity;
use BackedEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\Role;

final readonly class UpdateRoleAction
{
    use LogsActivity;

    /**
     * @phpstan-param Fluent<string, mixed> $params
     */
    public function execute(Role $role, Fluent $params): Role
    {
        return DB::transaction(function () use ($role, $params): Role {
            $oldPermissions = $role->permissions()->pluck('name')->all();

            $role->update([
                'name' => $params->get('name', $role->name),
                'description' => $params->get('description', $role->getAttribute('description')),
                'guard_name' => 'web',
            ]);

            $permissions = $params->get('permissions', []);
            assert(
                is_array($permissions) ||
                    $permissions instanceof BackedEnum ||
                    $permissions instanceof \Illuminate\Support\Collection ||
                    is_int($permissions) ||
                    $permissions instanceof \Spatie\Permission\Contracts\Permission ||
                    is_string($permissions)
            );

            $role->syncPermissions($permissions);

            $newPermissions = $role->permissions()->pluck('name')->all();
            $this->logUpdateActivity(
                'roles',
                $role,
                $role->getDirty(),
                'Atualizou um perfil',
                [
                    'permissions' => [$oldPermissions, $newPermissions],
                ]
            );

            return $role;
        });
    }
}
