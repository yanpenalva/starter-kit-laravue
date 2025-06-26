<?php declare(strict_types = 1);

use App\Http\Controllers\Api\Auth\{AuthController, PasswordController};
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth:sanctum');
    Route::get('auth/my-profile', 'myProfile')->name('auth.myProfile')->middleware('auth:sanctum');
});

Route::controller(PasswordController::class)->group(function () {
    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/reset-password', 'resetPassword')->name('reset-password');
    Route::get('/reset-password', 'verifyResetPassword')->name('verify-reset-password');
});
