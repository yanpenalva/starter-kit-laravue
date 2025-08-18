<?php

declare(strict_types = 1);

namespace App\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;

final class ShowActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
