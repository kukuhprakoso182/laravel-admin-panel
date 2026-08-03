<?php

namespace Database\Factories;

use App\Models\Icon;
use Illuminate\Database\Eloquent\Factories\Factory;

class IconFactory extends Factory
{
    protected $model = Icon::class;

    public function definition(): array
    {
        return [
            'value' => 'ri-' . $this->faker->unique()->word() . '-line',
            'section' => $this->faker->randomElement(['sidebar', 'action', 'status']),
            'is_active' => true,
        ];
    }
}
