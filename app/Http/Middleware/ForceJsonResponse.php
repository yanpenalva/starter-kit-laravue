<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ForceJsonResponse
{
    private $defaultAcceptHeader = 'application/json';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept') !== $this->defaultAcceptHeader) {
            $request->headers->set('Accept', $this->defaultAcceptHeader);
        }

        try {
            $response = $next($request);

            if ($response instanceof Response && $response->headers->get('Content-Type') !== $this->defaultAcceptHeader) {
                $response->headers->set('Content-Type', $this->defaultAcceptHeader);
            }

            return $response;
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
