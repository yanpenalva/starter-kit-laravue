<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;

class ForgotPasswordRequest extends FormRequest
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
        ];
    }
    /** @return \Illuminate\Support\Fluent<string, mixed> */
    public function fluentParams(?string $key = null, mixed $default = null): Fluent
    {
        /** @var array<string, mixed> $data */
        $data = null === $key
            ? $this->validated()
            : [(string) $key => $this->validated($key, $default)];

        return new Fluent($data);
    }
}
