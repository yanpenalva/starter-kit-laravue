<?php

declare(strict_types=1);

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->controller(UserController::class)->group(function () {
    Route::post('/register', 'register')->name('users.register');
    Route::get('/verify', 'verify')->name('users.verify')->middleware('signed');
    Route::get('/', 'index')->name('users.list')->middleware('auth:sanctum');
    Route::post('/', 'store')->name('users.create')->middleware('auth:sanctum');
    Route::get('/{id}', 'show')->name('users.show')->middleware('auth:sanctum');
    Route::put('/{id}', 'update')->name('users.update')->middleware('auth:sanctum');
    Route::delete('/{user}', 'destroy')->name('users.delete')->middleware('auth:sanctum');
});
