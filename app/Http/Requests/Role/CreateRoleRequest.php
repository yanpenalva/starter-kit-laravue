<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Rules\UniqueRoleNameRule;
use App\Traits\FailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class CreateRoleRequest extends FormRequest
{
    use FailedValidation;

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function prepareForValidation(): void
    {
        /** @var iterable<int, int|string>|null $rawPermissions */
        $rawPermissions = $this->permissions;

        $permissions = new Collection($rawPermissions ?? []);
        $isInteger = $permissions->contains(fn (mixed $item): bool => is_int($item));

        $this->merge([
            'permissions' => $isInteger
                ? $permissions->toArray()
                : $permissions->pluck('value')->toArray(),
        ]);
    }

    /**
     * @return array<string, array<int, string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                new UniqueRoleNameRule,
            ],
            'description' => ['max:258'],
            'permissions' => ['array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Perfil',
            'description' => 'Descrição',
            'permissions' => 'Permissões',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.string' => 'O :attribute deve conter uma palavra.',
            'name.unique' => 'O :attribute inserido já está em uso.',
            'description.max' => 'A :attribute inserida excede o limite de caracteres.',
            'permissions.array' => 'O :attribute deve ser uma lista',
        ];
    }
}
