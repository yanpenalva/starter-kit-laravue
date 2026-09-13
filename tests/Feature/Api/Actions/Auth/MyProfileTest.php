<?php

declare(strict_types = 1);

use App\Actions\Auth\MyProfileAction;
use Illuminate\Support\Facades\Auth;

describe('MyProfileAction (Feature)', function () {
    it('returns an empty array when no user is authenticated', function () {
        Auth::logout();

        expect(app(MyProfileAction::class)->execute())->toBe([]);
    });
})->group('feature', 'auth');
