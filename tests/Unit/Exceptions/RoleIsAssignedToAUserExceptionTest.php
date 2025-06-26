<?php declare(strict_types = 1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\RoleIsAssignedToUserException;
use Symfony\Component\HttpFoundation\Response;

describe('RoleIsAssignedToUserException', function () {
    it('should return the correct error message', function () {
        $exception = new RoleIsAssignedToUserException();

        expect($exception->getMessage())
          ->toBe('Existem usuário(s) vinculado(s) ao perfil. Exclusão não permitida!');
    })->group('exceptions');

    it('should return the correct error code', function () {
        $exception = new RoleIsAssignedToUserException();

        expect($exception->getCode())
          ->toBe(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->group('exceptions');

    it('should return the correct error type', function () {
        $exception = new RoleIsAssignedToUserException();

        expect($exception->error)
          ->toBe('O Perfil não é válido para exclusão.');
    })->group('exceptions');
});
