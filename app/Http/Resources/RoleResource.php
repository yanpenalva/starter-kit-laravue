<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\{Collection, Str};
use Spatie\Permission\Models\Permission;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property Collection<int, Permission>|null $permissions
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $guard_name
 *
 * @phpstan-consistent-constructor
 */
final class RoleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'shortDescription' => Str::words($this->description, 6, '...'),
            'createdAt' => $this->created_at,
            'permissions' => $this->getPermissionsForSelect(),
        ];
    }

    /**
     * Retrieve permissions formatted for a select field.
     *
     * @return array<array{value: int|null, label: string}>
     */
    protected function getPermissionsForSelect(): array
    {
        return ($this->permissions ?? collect())->map(
            fn (Permission $permission): array => [
                'value' => $permission->id,
                'label' => $permission->description ?? '',
            ]
        )->toArray();
    }
}
