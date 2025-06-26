<?php declare(strict_types = 1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\InvalidCredentialsException;
use Symfony\Component\HttpFoundation\Response;

describe('InvalidCredentialsException', function () {
    it('should return the correct error message', function () {
        $exception = new InvalidCredentialsException();

        expect($exception->getMessage())
            ->toBe('Usuário ou senha inválidos');
    });

    it('should return the correct error code', function () {
        $exception = new InvalidCredentialsException();

        expect($exception->getCode())
            ->toBe(Response::HTTP_UNAUTHORIZED);
    });
})->group('exceptions');
