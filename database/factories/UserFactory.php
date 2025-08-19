<?php

declare(strict_types = 1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('pt_BR')->name(),
            'email' => fake('pt_BR')->email(),
            'cpf' => mb_str_pad((string) random_int(10000000000, 99999999999), 11, '0', STR_PAD_LEFT),
            'active' => true,
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('admin'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => 'false',
        ]);
    }
}
