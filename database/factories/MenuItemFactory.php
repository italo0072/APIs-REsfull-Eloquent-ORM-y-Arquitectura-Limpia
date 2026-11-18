<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 3, 60),
            'is_available' => true,
            'image_url' => null,
            'preparation_time' => fake()->numberBetween(5, 40),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }
}
