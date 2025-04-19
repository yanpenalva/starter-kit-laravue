<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreateRoleRequest extends FormRequest
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
        $permissions = (new Collection($this->permissions))->pluck('value')->toArray();
        $this->merge([
            'permissions' => $permissions,
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
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.string' => 'O :attribute deve conter uma palavra.',
            'description.max' => 'A :attribute inserida excede o limite de caracteres.',
            'permissions.array' => 'O :attribute deve ser uma lista',
        ];
    }
}
