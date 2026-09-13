<?php

declare(strict_types = 1);

namespace App\Actions\Permission;

use App\DTO\Permission\PermissionGroupDTO;
use App\Enums\PermissionGroupEnum;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

final readonly class PermissionListAction
{
    /**
     * @return Collection<int, PermissionGroupDTO>
     */
    public function execute(): Collection
    {
        return Permission::query()
            ->select(['id', 'description', 'resource'])
            ->get()
            ->groupBy('resource')
            ->map(
                fn (EloquentCollection $permissions, string $group): PermissionGroupDTO => new PermissionGroupDTO(
                    $group,
                    PermissionGroupEnum::label($group),
                    $permissions
                )
            )
            ->values();
    }
}
