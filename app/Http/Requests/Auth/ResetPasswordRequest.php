<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;

final class ResetPasswordRequest extends FormRequest
{
    use FailedValidation;

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
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required|string',
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
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.exists' => 'Nenhum cadastro encontrado com o e-mail informado.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'O campo senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
            'token.required' => 'O token não foi fornecido.',
            'token.string' => 'O token contém o tipo inválido.',
        ];
    }

    /**
     * Retorna os dados validados encapsulados em um objeto Fluent.
     *
     * @return Fluent<string, mixed>
     */
    public function fluentParams(?string $key = null, mixed $default = null): Fluent
    {
        $validated = parent::validated($key, $default);
        assert(is_array($validated));

        if (! array_key_exists('password_confirmation', $validated)) {
            /** @var string|null $passwordConfirmation */
            $passwordConfirmation = $this->input('password_confirmation');
            $validated['password_confirmation'] = $passwordConfirmation;
        }

        /** @var array<string, mixed> $validated */
        return new Fluent($validated);
    }
}
