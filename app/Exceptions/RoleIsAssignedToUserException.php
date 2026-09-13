<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Throwable;

final class RoleIsAssignedToUserException extends Exception
{
    public string $error;

    public function __construct(
        string $message = 'Existem usuário(s) vinculado(s) ao perfil. Exclusão não permitida!',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->error = 'O Perfil não é válido para exclusão.';
    }
}
