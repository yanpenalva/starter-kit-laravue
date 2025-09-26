<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// @codeCoverageIgnoreStart
final class RedirectIfAuthenticated {
    public function handle(Request $request, Closure $next, string ...$guards): Response {
        $guards = collect($guards)->whenEmpty(fn () => collect([null]));

        return $guards->first(fn ($guard) => Auth::guard($guard)->check())
            ? redirect(RouteServiceProvider::HOME)->send()
            : $next($request);
    }
}
// @codeCoverageIgnoreEnd
