<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::get('/', 'index')->name('roles.list')->middleware('auth:sanctum');
    Route::post('/', 'store')->name('roles.create')->middleware('auth:sanctum');
    Route::get('/all', 'listAll')->name('roles.listAll')->middleware('auth:sanctum');
    Route::get('/{role}', 'show')->name('roles.view')->middleware('auth:sanctum');
    Route::match(['put', 'patch'], '/{role}', 'update')->name('roles.edit')->middleware('auth:sanctum');
    Route::delete('/{role}', 'destroy')->name('roles.delete')->middleware('auth:sanctum');
});
