<?php
declare(strict_types = 1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\UnactivatedUserException;
use Symfony\Component\HttpFoundation\Response;

describe('UnactivatedUserException', function () {
    it('should return the correct error message', function () {
        $exception = new UnactivatedUserException();

        expect($exception->getMessage())
            ->toBe('O Usuário não está ativo. Por favor contate o administrador do sistema.');
    });

    it('should return the correct error code', function () {
        $exception = new UnactivatedUserException();

        expect($exception->getCode())
            ->toBe(Response::HTTP_FORBIDDEN);
    });

    it('should return the correct error type', function () {
        $exception = new UnactivatedUserException();

        expect($exception->error)
            ->toBe('Usuário não ativado');
    });
})->group('exceptions');
