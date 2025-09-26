<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Enums\RolesEnum;
use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegisterExternalUserRequest extends FormRequest {
    use FailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'cpf' => ['required', new \App\Rules\ValidateCPF(), Rule::unique('users', 'cpf')->ignore($this->id)],
            'role' => [
                'required',
                Rule::in([
                    RolesEnum::GUEST->value,
                ]),
            ],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'min:8'],
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser string.',

            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email inserido não é válido.',
            'email.unique' => 'O email já foi cadastrado.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'O CPF já foi cadastrado.',

            'role.required' => 'O campo perfil é obrigatório.',
            'role.in' => 'O perfil selecionado é inválido.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'O campo senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',

            'password_confirmation.required' => 'O campo repetir senha é obrigatório.',
            'password_confirmation.min' => 'O campo repetir senha deve conter no mínimo 8 caracteres.',
        ];
    }
}
