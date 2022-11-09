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
        $quantity = $this->faker->numberBetween(10, 60);

        if ($quantity !== 0) {
            $status = 1;
        } else {
            $status = 0;
        }
        return [
            "name" => $this->faker->name(),
            "description" => $this->faker->paragraph(3, true),
            "price" => $this->faker->randomNumber(),
            "percent_sale" => $this->faker->numberBetween(1, 60),
            // "noteable" => $this->faker->sentence(6, true),
            "quantity" => $quantity,
            "status" => $status,
        ];
    }
}
