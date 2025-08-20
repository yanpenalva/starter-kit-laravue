<?php

declare(strict_types=1);

namespace App\Actions\Role;

use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\{Fluent, Str};
use Spatie\Permission\Models\Role;

final readonly class CreateRoleAction {
    use LogsActivity;

    /**
     * @param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params): Role {
        return DB::transaction(function () use ($params): Role {
            $name = $params->get('name');
            assert(is_string($name));

            $description = $params->get('description');
            assert($description === null || is_string($description));

            $permissions = $params->get('permissions', []);
            assert(is_array($permissions));

            $role = Role::create([
                'name' => $name,
                'guard_name' => 'web',
                'slug' => Str::slug($name),
                'description' => $description,
            ]);

            assert($role instanceof Role);

            $role->syncPermissions($permissions);

            $this->writeOnLog($role);

            return $role;
        });
    }

    private function writeOnLog(Role $role): void {
        $this->logGeneralActivity(
            activityName: 'roles',
            model: $role,
            description: sprintf(
                'Criou um novo perfil "%s" com %d permissões',
                $role->name,
                $role->permissions()->count()
            ),
            event: 'create'
        );
    }
}
