<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductQueryController extends Controller
{
    public function arrival()
    {
        $products = Product::orderBy("created_at", "DESC")->take(10)->get();

        return new ProductListCollection($products);
    }

    public function sale()
    { // Base on new arrival
        $products_sale = Product::where("percent_sale", "<>", "0")
            ->orderBy("created_at", "DESC")->take(10)->get();

        return new ProductListCollection($products_sale);
    }

    public function best()
    {
        // Count duplicate products
        $products_filter = DB::table("order_product")
            ->select("product_id", DB::raw('count(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy('count', 'DESC')
            ->get()
            ->take(20);

        $products_best_seller = [];

        for ($i = 0; $i < sizeof($products_filter); $i++) {
            $product = Product::where('id', "=", $products_filter[$i]->product_id)->first();

            if ($product->status === 0 || $product->deleted_at !== null) continue;

            $products_best_seller[$i]["productId"] = $product->id;
            $products_best_seller[$i]["name"] = $product->name;
            $products_best_seller[$i]["description"] = $product->description;
            $products_best_seller[$i]["price"] = $product->price;
            $products_best_seller[$i]["percentSale"] = $product->percent_sale;
            $products_best_seller[$i]["img"] = $product->img;
            $products_best_seller[$i]["quantity"] = $product->quantity;
        }

        return $products_best_seller;
    }

    public function trending(Request $request)
    {
        // $orders = Order::orderBy("created_at", "ASC")->take(100)->get();

        // $arr_orders = [];
        // $current_date = date("Y-m-d H:i:s");
        // $index = 0;

        // // Get all recent orders meet the require days (base on {day} calculated on second)
        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     if ((strtotime($current_date) - strtotime($orders[$i]['created_at'])) >= ((int) $request->day) * 60 * 60 * 24) {
        //         $arr_orders[$index]['orderId'] = $orders[$i]['id'];
        //         $index++;
        //     }
        // }

        // $arr_products = [];
        // $index = 0;
        // // Get all products store in order
        // for ($i = 0; $i < sizeof($arr_orders); $i++) {
        //     $products_in_order = DB::table("order_product")
        //         ->select("product_id", DB::raw('count(product_id) as count'))
        //         ->groupBy('product_id')
        //         ->orderBy('count', 'DESC')
        //         ->where("order_id", "=", $arr_orders[$i]['orderId'])
        //         ->get()
        //         ->take(20);

        //     if ($products_in_order->count() !== 0) {
        //         $arr_products[$index] = $products_in_order;
        //         $index++;
        //     }
        // }

        // $arr_products_filter = [];
        // $index = 0;

        // dd($arr_products[0][sizeof($arr_products[0])-1]);

        // // Filter duplicated products
        // for ($i = 0; $i < sizeof($arr_products); $i++) { // Loop go from the top
        //     for ($j = sizeof($arr_products)-1; $j > $i; $j++) { // Loop go from the bottom
        //         $stay = sizeof($arr_products[$j])-1;
        //         $answer = in_array($arr_products[$j][$stay]->product_id, (array) $arr_products[$i]);
        //         dd($answer);
        //         for ($k = 0; $k < sizeof($arr_products[$i]); $k++) { // Loop to into detail

        //         }
        //     }
        // }


        // return $arr_products[0][0]->id;
    }
}
