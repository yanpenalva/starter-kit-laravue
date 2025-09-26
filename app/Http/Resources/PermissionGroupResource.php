<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\DTO\Permission\PermissionGroupDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PermissionGroupDTO
 */
final class PermissionGroupResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'group' => $this->group,
            'label' => $this->label,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
