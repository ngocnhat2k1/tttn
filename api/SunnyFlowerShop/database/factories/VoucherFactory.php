<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->company(), // Uhh... I.. Don't.. Know...
            "usage" => $this->faker->numberBetween(1, 60),
            "percent" => $this->faker->numberBetween(1, 60),
            "expired_date" => $this->faker->dateTimeThisDecade(),
        ];
    }
}
