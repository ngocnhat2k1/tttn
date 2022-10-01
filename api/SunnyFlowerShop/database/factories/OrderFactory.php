<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Customer;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "customer_id" => Customer::factory(),
            "voucher_id" => Voucher::factory(),
            "date_order" => $this->faker->dateTimeThisDecade(),
            "address" => $this->faker->streetAddress(),
            "name_receiver" => $this->faker->name(),
            "phone_receiver" => $this->faker->tollFreePhoneNumber(),
            "total_price" => $this->faker->randomNumber(),
            "status" => $this->faker->randomElement(OrderStatusEnum::asArray()),
            "paid_type" => $this->faker->boolean()
        ];
    }
}
