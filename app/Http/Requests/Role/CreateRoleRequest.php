<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Traits\FailedValidation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB};

class CreateRoleRequest extends FormRequest
{
    use FailedValidation;

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function prepareForValidation(): void
    {
        $permissionsRaw = $this->permissions;

        assert(
            $permissionsRaw === null ||
                $permissionsRaw instanceof Arrayable ||
                is_iterable($permissionsRaw)
        );

        /** @var Arrayable<int|string, mixed>|iterable<int|string, mixed>|null $permissionsRaw */
        $permissionsArray = (new Collection($permissionsRaw))
            ->pluck('value')
            ->toArray();

        $this->merge([
            'permissions' => $permissionsArray,
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                function (string $attribute, mixed $value, callable $fail): void {
                    assert(is_string($value));
                    $trimmedValue = mb_trim($value);

                    $query = DB::table('roles')
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmedValue)]);

                    if ($this->role !== null) {
                        assert(is_object($this->role) && property_exists($this->role, 'id'));
                        $query->where('id', '!=', $this->role->id);
                    }

                    if ($query->exists()) {
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
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.string' => 'O :attribute deve conter uma palavra.',
            'description.max' => 'A :attribute inserida excede o limite de caracteres.',
            'permissions.array' => 'O :attribute deve ser uma lista',
        ];
    }
}
