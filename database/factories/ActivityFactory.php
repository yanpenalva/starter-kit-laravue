<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Activitylog\Models\Activity;

final class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'log_name' => 'system',
            'description' => $this->faker->sentence(),
            'event' => $this->faker->randomElement(['create', 'update', 'delete', 'view']),
            'causer_id' => null,
            'causer_type' => null,
            'subject_id' => null,
            'subject_type' => null,
            'properties' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withCauser(User $user): self
    {
        return $this->state([
            'causer_id' => $user->id,
            'causer_type' => User::class,
        ]);
    }

    public function withSubject(User $user): self
    {
        return $this->state([
            'subject_id' => $user->id,
            'subject_type' => User::class,
        ]);
    }
}
