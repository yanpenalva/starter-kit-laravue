<?php

declare(strict_types = 1);

namespace App\Actions\Role;

use App\Exceptions\RoleIsAssignedToUserException;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

final readonly class DeleteRoleAction
{
    use LogsActivity;
    public function execute(Role $role): bool
    {
        return DB::transaction(function () use ($role): bool {

            throw_if($role->users()->exists(), RoleIsAssignedToUserException::class);

            $role->users()->detach();
            $role->permissions()->detach();

            $this->logDeleteActivity('roles', $role, 'Excluiu um Perfil');

            return (bool) $role->delete();
        });
    }
}
