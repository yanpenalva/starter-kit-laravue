<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;

// @codeCoverageIgnoreStart

final class AuthenticationException extends Exception
{
    /**
     * @var array<int, string>
     */
    protected array $guards;

    protected ?string $redirectTo;

    /**
     * @param  array<int, string>  $guards
     */
    public function __construct(string $message = 'Não Autenticado', array $guards = [], ?string $redirectTo = null)
    {
        parent::__construct($message);

        $this->guards = $guards;
        $this->redirectTo = $redirectTo;
    }

    /**
     * @return array<int, string>
     */
    public function guards(): array
    {
        return $this->guards;
    }

    public function redirectTo(): ?string
    {
        return $this->redirectTo;
    }
}
// @codeCoverageIgnoreEnd
