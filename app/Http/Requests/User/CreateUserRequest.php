<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\{RolesEnum, StatusEnum};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{Fluent, Str};
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'cpf' => [
                'required',
                'string',
                new \App\Rules\ValidateCPF(),
                Rule::unique('users', 'cpf')->ignore($this->id),
            ],
            'active' => ['required', Rule::in(StatusEnum::ENABLED, StatusEnum::DISABLED)],
            'role_id' => ['required', 'exists:roles,id'],
            'send_random_password' => ['boolean'],
        ];

        if (!$this->send_random_password && $this->role_slug !== RolesEnum::ADMINISTRATOR->value) {
            $rules['password'] = ['required', 'min:8'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Senha',
            'role_id' => 'Perfil',
            'active' => 'Status',
            'cpf' => 'CPF',
            'registration' => 'Matrícula',
            'send_random_password' => 'Enviar senha aleatória por e-mail',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cpf.unique' => 'CPF já cadastrado no SISTEX, realize o login com suas credenciais.',
            'email.unique' => 'O e-mail já foi cadastrado.',
            'email.email' => 'O :attribute inserido não é válido.',
            'boolean' => 'O campo :attribute não foi informado',
            'required' => 'O campo :attribute é obrigatório.',
            'exists' => 'O :attribute é inválido.',
            'max' => 'O :attribute inserido excede o limite de caracteres.',
            'unique' => 'O :attribute inserido já está em uso.',
            'in' => 'O :attribute inserido não é válido.',
            'password.min' => 'O campo Senha deve conter no mínimo 8 caracteres.',
        ];
    }

    /**
     * Retorna os dados validados encapsulados em um objeto Fluent.
     *
     * @param string|null $key
     * @return Fluent<string, mixed>
     */
    public function fluentParams(?string $key = null): Fluent
    {
        /** @var array<string, mixed> $validated */
        $validated = is_array($this->validated($key)) ? $this->validated($key) : [];

        return new Fluent(array_merge($validated, [
            'password' => $this->has('send_random_password') ? Str::password(8) : $this->password,
        ]));
    }

}
