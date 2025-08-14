<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Traits\FailedValidation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{Collection, Fluent};
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Str;
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
                function (string $attribute, mixed $value, Closure $fail) use ($role): void {
                    if (!is_string($value)) {
                        return;
                    }

                    $trimmedValue = mb_trim($value);

                    $exists = DB::table('roles')
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmedValue)])
                        ->when(
                            $role !== null,
                            fn ($query) => $query->where('id', '!=', $role?->id) // safe access
                        )
                        ->exists();

                    if ($exists) {
                        $fail('Este nome de perfil já está em uso. Escolha outro nome.');
                    }
                },
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

        /** @var Str $str */
        $str = str();

        return new Fluent([
            ...$validated,
            'guard_name' => 'web',
            'slug' => $str->slug($name),
            'role' => $role,
        ]);
    }
}
