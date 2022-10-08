<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "description" => $this->faker->paragraph(3, true),
            "price" => $this->faker->randomNumber(),
            "percent_sale" => $this->faker->numberBetween(1, 60),
            // "noteable" => $this->faker->sentence(6, true),
            "quantity" => $this->faker->numberBetween(10, 60),
            "status" => $this->faker->boolean(),
        ];
    }
}
