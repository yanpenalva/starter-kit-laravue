<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;

final class ForgotPasswordRequest extends FormRequest
{
    use FailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    /**
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

    /**
     * @return Fluent<string, mixed>
     */
    public function fluentParams(?string $key = null, mixed $default = null): Fluent
    {
        /** @var array<string,mixed> $data */
        $data = $key
            ? [$key => $this->input($key)]
            : $this->only(['email']);

        return new Fluent($data);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('email')) {
            $emailInput = $this->input('email');

            if (is_string($emailInput)) {
                $this->merge([
                    'email' => mb_strtolower(mb_trim($emailInput)),
                ]);
            }
        }
    }
}
