<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\{Auth, Session};

final readonly class LogoutAction {
    /**
     * @return array<string, string>
     */
    public function execute(): array {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        Session::invalidate();
        Session::regenerate();

        if ($user) {
            $user->tokens()->delete();
        }

        return ['message' => 'Volte Sempre'];
    }
}
