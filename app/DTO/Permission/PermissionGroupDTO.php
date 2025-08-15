<?php

declare(strict_types = 1);

namespace App\DTO\Permission;

use App\DTO\AbstractDTO;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Spatie\Permission\Models\Permission;

/**
 * @phpstan-type PermissionCollection EloquentCollection<int, Permission>
 */
final class PermissionGroupDTO extends AbstractDTO
{
    /**
     * @param PermissionCollection $permissions
     */
    public function __construct(
        public string $group,
        public string $label,
        public EloquentCollection $permissions
    ) {
    }
}
