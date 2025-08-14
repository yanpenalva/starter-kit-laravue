<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Rules\UniqueRoleNameRule;
use App\Traits\FailedValidation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{Collection, Fluent, Str};
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

final class UpdateRoleRequest extends FormRequest
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
     * @return array<string, array<int, string|ValidationRule|Closure>>
     */
    public function rules(): array
    {
        /** @var Role|null $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                new UniqueRoleNameRule($role?->id ? (int) $role->id : null),
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
            'name.required' => 'O :attribute é obrigatório.',
            'name.string' => 'O :attribute deve conter uma palavra.',
            'name.unique' => 'O :attribute inserido já está em uso.',
            'description.max' => 'A :attribute inserida excede o limite de caracteres.',
            'permissions.array' => 'O :attribute deve ser uma lista',
        ];
    }

    /**
     * @param array<string>|string|int|null $key
     * @return Fluent<string, mixed>
     */
    public function fluent($key = null): Fluent
    {
        /** @var Role|null $role */
        $role = $this->route('role');

        /** @var array<string, mixed> $validated */
        $validated = $this->validated($key);

        /** @var mixed $rawName */
        $rawName = $this->input('name');

        assert(is_scalar($rawName) || $rawName === null);

        /** @var string $name */
        $name = (string) ($rawName ?? '');

        return new Fluent([
            ...$validated,
            'guard_name' => 'web',
            'slug' => Str::slug($name),
            'role' => $role,
        ]);
    }
}
