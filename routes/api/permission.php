<?php declare(strict_types = 1);

use App\Http\Controllers\Api\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
    Route::get('/', 'index')->name('permissions.list')->middleware('auth:sanctum');
});
