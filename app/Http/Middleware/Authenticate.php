<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

final class Authenticate extends Middleware {
    // @codeCoverageIgnoreStart
    protected function redirectTo(Request $request): ?string {
        throw new AuthenticationException('Usuário não autenticado. Realize o login para acessar o sistema');
    }
    // @codeCoverageIgnoreEnd
}
