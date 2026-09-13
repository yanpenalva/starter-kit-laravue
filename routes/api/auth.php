<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\Auth\{AuthController, PasswordController};
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->middleware('throttle:login')->name('login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('logout');
    Route::get('/auth/my-profile', 'myProfile')->middleware('auth:sanctum')->name('auth.myProfile');
});

Route::controller(PasswordController::class)->group(function () {
    Route::post('/forgot-password', 'forgotPassword')
        ->middleware('throttle:2,1')
        ->name('forgot-password');

    Route::post('/reset-password', 'resetPassword')->middleware('throttle:password-reset')->name('reset-password');
    Route::get('/reset-password', 'verifyResetPassword')->name('verify-reset-password');
});
