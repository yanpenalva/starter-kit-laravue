<?php

declare(strict_types = 1);

namespace App\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;

final class IndexActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sortBy' => ['nullable', 'string', 'in:id,logName,description,causer,subject,createdAt'],
            'order' => ['nullable', 'string', 'in:asc,desc'],
            'rowsPerPage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'paginated' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function validatedFluent(): Fluent
    {
        return new Fluent($this->validated());
    }
}
