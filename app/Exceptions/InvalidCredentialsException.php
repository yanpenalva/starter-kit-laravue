<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Throwable;

final class InvalidCredentialsException extends Exception
{
    public string $error;

    public function __construct(
        string $message = 'Usuário ou senha inválidos',
        int $code = Response::HTTP_UNAUTHORIZED,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->error = 'Credenciais inválidas';
    }
}
