<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Permission;

use App\Actions\Permission\PermissionListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionGroupResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;

final class PermissionController extends Controller {
    public function index(): AnonymousResourceCollection {
        $this->authorize('index', Permission::class);

        $permissions = app(PermissionListAction::class)->execute();

        return PermissionGroupResource::collection($permissions);
    }
}
