<?php declare(strict_types = 1);

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
            'cpf' => preg_replace('/\D/', '', fake('pt_BR')->cpf()),
            'active' => true,
            'email_verified_at' => now(),
            'password' => 'admin',
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
