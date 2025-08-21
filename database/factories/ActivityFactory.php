<?php

declare(strict_types = 1);

namespace Database\Factories;

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
            'subject_id' => null,
            'properties' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
