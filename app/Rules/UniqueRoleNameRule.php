<?php

declare(strict_types = 1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

final class UniqueRoleNameRule implements ValidationRule
{
    public function __construct(private readonly ?int $ignoreId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $trimmedValue = mb_trim($value);

        $exists = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmedValue)])
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Este nome de perfil já está em uso. Escolha outro nome.');
        }
    }
}
