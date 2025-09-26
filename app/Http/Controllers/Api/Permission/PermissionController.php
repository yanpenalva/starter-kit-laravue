<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Permission;

use App\Actions\Permission\PermissionListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionGroupResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PermissionController extends Controller {
    public function index(): AnonymousResourceCollection {
        $permissions = app(PermissionListAction::class)->execute();

        return PermissionGroupResource::collection($permissions);
    }
}
