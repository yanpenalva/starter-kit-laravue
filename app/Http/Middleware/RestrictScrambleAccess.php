<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

// @codeCoverageIgnoreStart
final class RestrictScrambleAccess {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($this->isRestrictedEnvironment()) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
    /**
     * Check if the current environment is restricted.
     */
    private function isRestrictedEnvironment(): bool {
        return in_array(config('app.env'), ['production', 'staging'], true);
    }
    /**
     * Return a forbidden response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function forbiddenResponse(?string $message = null) {
        return response()->json([
            'code' => HttpFoundationResponse::HTTP_FORBIDDEN,
            'message' => $message ?? 'O acesso a esta rota está restrito neste ambiente.',
        ], HttpFoundationResponse::HTTP_FORBIDDEN);
    }
}
// @codeCoverageIgnoreEnd
