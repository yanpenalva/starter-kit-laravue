<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;

class LoginRequest extends FormRequest
{
    use FailedValidation;
    /**
     * @phpstan-type ParamsArray array{email: string, password: string}
     */

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
            'email' => 'required|email',
            'password' => 'required|string',
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
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser uma string.',
        ];
    }

    /**
     * Retorna os dados validados encapsulados em um objeto Fluent.
     *
     * @return Fluent<string, mixed>
     */
    public function fluentParams(): Fluent
    {
        return new Fluent($this->validated());
    }
}
