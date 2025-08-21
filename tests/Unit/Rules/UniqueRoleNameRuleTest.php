<?php

declare(strict_types = 1);

use App\Rules\UniqueRoleNameRule;
use Illuminate\Support\Facades\{DB, Validator};

beforeEach(function () {
    Mockery::close();
});

describe('UniqueRoleNameRule (Unit)', function () {
    it('passes when the role name is unique', function () {
        DB::shouldReceive('table->whereRaw->when->exists')
            ->once()
            ->andReturnFalse();

        $validator = Validator::make(
            ['name' => 'Gerente'],
            ['name' => [new UniqueRoleNameRule()]]
        );

        expect($validator->passes())->toBeTrue();
    });

    it('fails when the role name already exists (case insensitive)', function () {
        DB::shouldReceive('table->whereRaw->when->exists')
            ->once()
            ->andReturnTrue();

        $validator = Validator::make(
            ['name' => 'administrador'],
            ['name' => [new UniqueRoleNameRule()]]
        );

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->first('name'))
            ->toBe('Este nome de perfil já está em uso. Escolha outro nome.');
    });

    it('passes when updating and ignoring the current role id', function () {
        DB::shouldReceive('table->whereRaw->when->exists')
            ->once()
            ->andReturnFalse();

        $validator = Validator::make(
            ['name' => 'Administrador'],
            ['name' => [new UniqueRoleNameRule(ignoreId: 1)]]
        );

        expect($validator->passes())->toBeTrue();
    });

    it('fails when updating and trying to use another role name', function () {
        DB::shouldReceive('table->whereRaw->when->exists')
            ->once()
            ->andReturnTrue();

        $validator = Validator::make(
            ['name' => 'Editor'],
            ['name' => [new UniqueRoleNameRule(ignoreId: 1)]]
        );

        expect($validator->fails())->toBeTrue();
    });

    it('passes when the value is not a string', function () {
        DB::shouldReceive('table')->never();

        $validator = Validator::make(
            ['name' => 123],
            ['name' => [new UniqueRoleNameRule()]]
        );

        expect($validator->passes())->toBeTrue();
    });

    it('fails when the role name has leading/trailing spaces matching existing', function () {
        DB::shouldReceive('table->whereRaw->when->exists')
            ->once()
            ->andReturnTrue();

        $validator = Validator::make(
            ['name' => '  Administrador  '],
            ['name' => [new UniqueRoleNameRule()]]
        );

        expect($validator->fails())->toBeTrue();
    });
})->group('unit', 'rules');
