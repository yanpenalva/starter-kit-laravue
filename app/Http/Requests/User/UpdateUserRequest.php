<?php

declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Enums\StatusEnum;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            $this->cpfRule(),
            $this->passwordRule(),
            $this->roleSlugRule()
        );
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Senha',
            'role_id' => 'Perfil',
            'role_slug' => 'Perfil',
            'active' => 'Status',
            'cpf' => 'CPF',
            'registration' => 'Matrícula',
            'notify_status' => 'Envio de Notificação',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var string $appName */
        $appName = config('app.name');

        return [
            'cpf.unique' => "CPF já cadastrado no {$appName}, realize o login com suas credenciais.",
            'email.unique' => 'O e-mail já foi cadastrado.',
            'email.email' => 'O :attribute inserido não é válido.',
            'boolean' => 'O campo :attribute não é booleano.',
            'required' => 'O campo :attribute é obrigatório.',
            'exists' => 'O :attribute é inválido.',
            'max' => 'O :attribute inserido excede o limite de caracteres.',
            'unique' => 'O :attribute inserido já está em uso.',
            'in' => 'O :attribute inserido não é válido.',
            'password.min' => 'O campo Senha deve conter no mínimo 8 caracteres.',
        ];
    }

    /**
     * Retorna os dados validados usando Fluent, com opção de filtrar por chave.
     *
     * @param string|null $key
     * @return Fluent<string, mixed>
     */
    public function fluentParams(?string $key = null): Fluent
    {
        $validated = $this->validated($key);

        /** @var array<string, mixed> $validated */
        $validated = is_array($validated) ? $validated : [];

        return new Fluent($validated);
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->route('id'))],
            'active' => ['required', Rule::in(StatusEnum::ENABLED, StatusEnum::DISABLED)],
            'role_id' => ['sometimes', 'required', 'exists:roles,id'],
            'notify_status' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function cpfRule(): array
    {
        if (!$this->filled('cpf')) {
            return [];
        }

        return [
            'cpf' => [
                'required',
                'string',
                new \App\Rules\ValidateCPF(),
                Rule::unique('users', 'cpf')
                    ->where(function (Builder $query): void {
                        $query->whereExists(function (Builder $subQuery): void {
                            $subQuery->select(DB::raw(1))
                                ->from('role_user')
                                ->whereColumn('role_user.user_id', 'users.id')
                                ->where('role_user.role_id', $this->role_id);
                        });
                    })
                    ->ignore($this->route('id')),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function passwordRule(): array
    {
        return $this->filled('password') ? ['password' => ['nullable', 'min:8']] : [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function roleSlugRule(): array
    {
        return $this->filled('role_slug') ? ['role_slug' => ['required']] : [];
    }

}
