<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->word());
        $slug = strtolower($name);

        return [
            'parent_id' => null,
            'icon_id' => null,
            'name' => $name,
            'link' => '/' . $slug,
            'link_alias' => $slug . '.index',
            'order' => $this->faker->numberBetween(1, 99),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function childOf(Menu $parent): static
    {
        return $this->state(fn () => ['parent_id' => $parent->id]);
    }
}
