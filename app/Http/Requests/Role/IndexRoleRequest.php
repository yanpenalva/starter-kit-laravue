<?php

declare(strict_types = 1);

namespace App\Http\Requests\Role;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IndexRoleRequest extends FormRequest
{
    use FailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function prepareForValidation(): void
    {

        $this->merge([
            'name' => $this->get('name', ''),
            'limit' => $this->get('limit', 10),
            'page' => $this->get('page', 1),
            'column' => $this->get('column', 'id'),
            'order' => $this->get('order', 'desc'),
            'search' => $this->get('search', null),
            'paginated' => $this->get('paginated', 1),
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
                'sometimes',
                'string',
            ],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'column' => [
                'sometimes',
                'string',
                'in:id,name,description',
            ],
            'order' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],
            'search' => [
                'sometimes',
                'string',
                'nullable',
            ],
            'paginated' => [
                'sometimes',
                'integer',
                'in:0,1',
            ],
        ];
    }
    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O :attribute deve conter uma palavra.',
            'limit.integer' => 'O :attribute deve ser um inteiro.',
            'limit.min' => 'O :attribute deve conter ao menos 1.',
            'limit.max' => 'O :attribute deve conter no máximo 100.',
            'page.integer' => 'O :attribute deve ser um inteiro.',
            'page.min' => 'O :attribute deve conter ao menos 1.',
            'column.string' => 'O :attribute deve ser uma string.',
            'column.in' => 'O :attribute deve ser um dos seguintes: id, name, description.',
            'order.string' => 'O :attribute deve ser uma string.',
            'order.in' => 'O :attribute deve ser um dos seguintes: asc, desc.',
            'search.string' => 'O :attribute deve ser uma string.',
        ];
    }
    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Perfil',
            'limit' => 'Limite',
            'page' => 'Página',
            'column' => 'Coluna',
            'order' => 'Ordem',
            'search' => 'Pesquisa',
            'paginated' => 'Paginado',
        ];
    }
}
