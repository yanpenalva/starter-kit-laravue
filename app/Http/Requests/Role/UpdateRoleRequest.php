<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{Collection, Fluent};
use Illuminate\Support\Facades\DB;

class UpdateRoleRequest extends FormRequest
{
    use FailedValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation(): void
    {
        /** @var \Illuminate\Support\Collection<int, mixed> $permissions */
        $permissions = new Collection($this->permissions);
        $isInteger = $permissions->contains(function ($item) {
            return is_int($item);
        });

        $this->merge([
            'permissions' => $isInteger ? $permissions->toArray() : $permissions->pluck('value')->toArray(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $trimmedValue = mb_trim($value);
                    $exists = DB::table('roles')
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmedValue)])
                        ->when($this->role, fn ($query) => $query->where('id', '!=', $this->role->id))
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
     * Retorna instância Fluent com os dados validados acrescentando atributos adicionais.
     *
     * @param mixed|null $key
     * @return Fluent<string, mixed>
     */
    public function fluent($key = null): Fluent
    {

        return new Fluent([
            ...$this->validated($key),
            'guard_name' => 'web',
            'slug' => str()->slug($this->name),
        ]);
    }

}
