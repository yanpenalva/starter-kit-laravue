<?php

declare(strict_types = 1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule as Rule;

final class ValidateCPF implements Rule {
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $value = is_string($value) ? $value : '';

        $cpf = preg_replace('/\D/', '', $value) ?? '';

        if (mb_strlen($cpf) !== 11) {
            $fail($this->message());

            return;
        }

        if (preg_match("/^{$cpf[0]}{11}$/", $cpf)) {
            $fail($this->message());

            return;
        }

        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int) ($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cpf[9] !== (string) $firstCheckDigit) {
            $fail($this->message());

            return;
        }

        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += (int) ($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondCheckDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cpf[10] !== (string) $secondCheckDigit) {
            $fail($this->message());
        }
    }

    public function message(): string {
        return 'O :attribute inserido não é um CPF válido.';
    }
}
