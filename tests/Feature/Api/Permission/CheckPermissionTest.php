<?php declare(strict_types = 1);

use App\Http\Middleware\CheckPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Route};

describe('CheckPermission Middleware', function () {
    it('should return an access denied error', function () {
        $userAuth = User::factory()->create();

        Auth::login($userAuth);

        Route::shouldReceive('currentRouteName')->andReturn('view_permission');

        $request = Request::create('/test', 'GET');
        $middleware = new CheckPermission();

        $middleware->handle($request, function ($req) {
        });
    })->throws(Exception::class, 'Você não tem permissão para realizar esta ação.');
})->group('middleware');
