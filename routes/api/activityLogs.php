<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\Log\LogController;
use Illuminate\Support\Facades\Route;

Route::prefix('activity-logs')->controller(LogController::class)->group(function () {
    Route::get('/', 'index')->name('activity_logs.list')->middleware('auth:sanctum');
    Route::get('/{id}', 'show')->name('activity_logs.show')->middleware('auth:sanctum');
});
