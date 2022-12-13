<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Order::factory()
        //     ->count(10)
        //     // ->hasProducts(5)
        //     ->hasAttached(
        //         Product::factory()->count(5),
        //         [
        //             "quantity" => rand(1, 10),
        //             "price" => rand(100000, 1000000),
        //             "percent_sale" => rand(1, 60)
        //         ]
        //     )
        //     ->create();

        // Order::factory()
        //     ->count(5)
        //     ->hasAttached(
        //         Product::factory()->count(4),
        //         [
        //             "quantity" => rand(1, 10),
        //             "price" => rand(100000, 1000000),
        //             "percent_sale" => rand(1, 60)
        //         ]
        //     )
        //     ->create();
    }
}
