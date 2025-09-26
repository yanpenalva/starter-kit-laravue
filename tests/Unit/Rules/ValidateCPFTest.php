<?php
declare(strict_types = 1);

use App\Rules\ValidateCPF;
use Illuminate\Support\Facades\Validator;

describe('ValidateCPF (Unit)', function () {
    it('passes for a valid CPF', function () {
        $data = ['cpf' => '328.131.630-15']; // CPF válido
        $validator = Validator::make($data, ['cpf' => [new ValidateCPF()]]);

        expect($validator->passes())->toBeTrue();
    });

    it('fails for CPF with all same digits', function () {
        $data = ['cpf' => '111.111.111-11'];
        $validator = Validator::make($data, ['cpf' => [new ValidateCPF()]]);

        expect($validator->fails())->toBeTrue();
    });

    it('fails for CPF with incorrect check digits', function () {
        $data = ['cpf' => '328.131.630-00'];
        $validator = Validator::make($data, ['cpf' => [new ValidateCPF()]]);

        expect($validator->fails())->toBeTrue();
    });

    it('fails for CPF with less than 11 digits', function () {
        $data = ['cpf' => '123.456.789'];
        $validator = Validator::make($data, ['cpf' => [new ValidateCPF()]]);

        expect($validator->fails())->toBeTrue();
    });

    it('fails for empty CPF', function () {
        $data = ['cpf' => ''];
        $validator = Validator::make($data, ['cpf' => ['required', new ValidateCPF()]]);

        expect($validator->fails())->toBeTrue();
    });

    it('fails when second check digit is invalid', function () {
        $cpfWithInvalidSecondDigit = '328.131.630-10';

        $data = ['cpf' => $cpfWithInvalidSecondDigit];

        $validator = Validator::make($data, ['cpf' => ['required', new ValidateCPF()]]);

        expect($validator->fails())->toBeTrue();
    });

})->group('unit', 'rules');
