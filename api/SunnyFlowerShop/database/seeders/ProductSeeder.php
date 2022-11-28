<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()
            ->count(10)
            ->hasCategories(1)
            ->create();

        Product::factory()
            ->count(15)
            ->hasCategories(1)
            ->create();

        Product::factory()
            ->count(12)
            ->hasCategories(1)
            ->create();
    }
}
