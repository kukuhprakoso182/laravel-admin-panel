<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'subject_type' => User::class,
            'subject_id' => (string) $this->faker->uuid(),
            'description' => $this->faker->sentence(),
            'properties' => null,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => now(),
        ];
    }
}
