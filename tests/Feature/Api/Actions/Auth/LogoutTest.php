<?php declare(strict_types = 1);

use App\Actions\Auth\LogoutAction;
use App\Models\User;
use Illuminate\Support\Facades\{Auth};

describe('LogoutAction (Feature)', function () {
    it(
        'logs out authenticated user and deletes tokens',
        function () {
            $user = User::factory()->create();

            Auth::guard('web')->login($user);

            $user->tokens()->create([
                'name' => 'test-token',
                'token' => hash('sha256', 'dummy'),
                'abilities' => ['*'],
            ]);

            $result = app(LogoutAction::class)->execute();

            expect(Auth::guard('web')->check())->toBeFalse();
            expect($result)->toBeArray()->toMatchArray(['message' => 'Volte Sempre']);
            expect($user->tokens()->count())->toBe(0);
        }
    );

    it(
        'does not fail when user is already unauthenticated',
        function () {
            Auth::guard('web')->logout();

            $result = app(LogoutAction::class)->execute();

            expect(Auth::guard('web')->check())->toBeFalse();
            expect($result)->toMatchArray(['message' => 'Volte Sempre']);
        }
    );
})->group('feature', 'auth');
