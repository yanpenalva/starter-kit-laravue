<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
    }

    public function render($request, Throwable $exception): mixed
    {
        $status = match (true) {
            $exception instanceof InvalidCredentialsException => (int) ($exception->getCode() ?: Response::HTTP_UNAUTHORIZED),
            $exception instanceof AuthenticationException => (int) Response::HTTP_UNAUTHORIZED,
            $exception instanceof UnactivatedUserException => (int) ($exception->getCode() ?: Response::HTTP_FORBIDDEN),
            $exception instanceof RoleIsAssignedToUserException => (int) Response::HTTP_UNPROCESSABLE_ENTITY,
            $exception instanceof ModelNotFoundException => (int) Response::HTTP_NOT_FOUND,
            default => (int) Response::HTTP_INTERNAL_SERVER_ERROR,
        };
        Log::info('Status Code: ' . $status);

        if ($status !== Response::HTTP_INTERNAL_SERVER_ERROR || $exception->getCode() !== 0) {
            return response()->json([
                'error' => $exception->error ?? 'Erro',
                'message' => $exception instanceof AuthenticationException
                    ? 'Não autenticado, faça login para continuar.'
                    : ($exception->getMessage() ?: 'Um erro inesperado aconteceu. Por favor, tente novamente.'),
            ], $status);
        }

        return parent::render($request, $exception);
    }
}
