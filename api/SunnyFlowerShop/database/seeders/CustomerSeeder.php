<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Customer::factory()
        //     ->count(50)
        //     ->hasOrders(10)
        //     ->has(Product::factory()->count(10), "customer_product_favorite")
        //     // ->has(Product::factory()->count(10), "customer_product_feedback")
        //     ->hasAttached(
        //         Product::factory()->count(10),
        //         [
        //             // 'quality' => rand(1, 5),
        //             'created_at' => date("Y-m-d H:i:s"),
        //             'updated_at' => date("Y-m-d H:i:s"),
        //         ],
        //         "customer_product_feedback"
        //     )
        //     // ->has(Product::factory()->count(5), "customer_product_cart")
        //     ->hasAttached(
        //         Product::factory()->count(5),
        //         [
        //             'quantity' => rand(1, 10),
        //         ],
        //         "customer_product_cart"
        //     )
        //     ->hasAddresses(3)
        //     ->create();

        // Customer::factory()
        //     ->count(20)
        //     ->hasOrders(4)
        //     ->has(Product::factory()->count(8), "customer_product_favorite")
        //     ->hasAttached(
        //         Product::factory()->count(5),
        //         [
        //             // 'quality' => rand(1, 5),
        //             'created_at' => date("Y-m-d H:i:s"),
        //             'updated_at' => date("Y-m-d H:i:s"),
        //         ],
        //         "customer_product_feedback"
        //     )
        //     // ->has(Product::factory()->count(5), "customer_product_cart")
        //     ->hasAttached(
        //         Product::factory()->count(4),
        //         [
        //             'quantity' => rand(1, 10),
        //         ],
        //         "customer_product_cart"
        //     )
        //     ->hasAddresses(2)
        //     ->create();

        // Customer::factory()
        //     ->count(5)
        //     ->has(Product::factory()->count(12), "customer_product_favorite")
        //     ->hasAttached(
        //         Product::factory()->count(5),
        //         [
        //             'quantity' => rand(1, 10),
        //         ],
        //         "customer_product_cart"
        //     )
        //     ->hasAddresses(3)
        //     ->create();
    }
}
