<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Throwable;

class UnactivatedUserException extends Exception
{
    /**
     * @var string
     */
    public string $error;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'O Usuário não está ativo. Por favor contate o administrador do sistema.',
        int $code = Response::HTTP_FORBIDDEN,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->error = 'Usuário não ativado';
    }
}
