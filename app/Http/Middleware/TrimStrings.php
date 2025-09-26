<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

// @codeCoverageIgnoreStart
final class TrimStrings extends Middleware {
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
// @codeCoverageIgnoreEnd
