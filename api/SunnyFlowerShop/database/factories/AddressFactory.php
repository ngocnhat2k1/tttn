<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "first_name_receiver" => $this->faker->firstName(),
            "last_name_receiver" => $this->faker->lastName(),
            "phone_receiver" => $this->faker->tollFreePhoneNumber(),
            "street_name" => $this->faker->streetName(),
            "district" => $this->faker->buildingNumber(), // I have no idea why i use this
            "ward" => $this->faker->state(), // No idea either
            "city" => $this->faker->city(),
        ];
    }
}
