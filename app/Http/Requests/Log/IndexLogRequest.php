<?php

declare(strict_types=1);

namespace App\Http\Requests\Log;

use App\Traits\FailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class IndexLogRequest extends FormRequest {
    use FailedValidation;

    public function authorize(): bool {
        return Auth::check();
    }

    public function prepareForValidation(): void {
        $this->merge([
            'limit' => $this->get('limit', 10),
            'page' => $this->get('page', 1),
            'order' => $this->get('order', 'desc'),
            'column' => $this->get('column', 'id'),
            'search' => $this->get('search', ''),
            'paginated' => $this->get('paginated', 1),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array {
        return [
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'column' => [
                'sometimes',
                'string',
                'in:description,event,causer,subject,createdAt'
            ],
            'order' => ['sometimes', 'string', 'in:asc,desc'],
            'search' => ['sometimes', 'string', 'nullable'],
            'paginated' => ['sometimes', 'integer', 'in:0,1'],
        ];
    }
}
