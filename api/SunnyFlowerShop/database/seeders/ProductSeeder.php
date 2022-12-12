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
        $file = explode("\\", dirname(__FILE__));
        $path_get = $file[0];
        for ($i = 1; $i < sizeof($file) - 1; $i++) {
            $path_get = $path_get . "\\" . $file[$i];
        }

        $path = $path_get . "\\" . "products.json";
        // $path = $path_get . "\\" . "camera_edited.json"; // edited camera.json file

        // Read the JSON file 
        $json = file_get_contents($path);

        // Decode the JSON file
        $json_data = json_decode($json, true);

        for ($i = 0; $i < sizeof($json_data); $i++) {
            $product_value = [
                'name' => $json_data[$i]['name'],
                'description' => $json_data[$i]['description'],
                'price' => $json_data[$i]['price'],
                'img' => $json_data[$i]['img'],
                'quantity' => $json_data[$i]['quantity']
            ];

            $product = Product::create($product_value);

            $category_id = $json_data[$i]['categoryId'];

            $product->categories()->attach($category_id);
        }
    }
}
