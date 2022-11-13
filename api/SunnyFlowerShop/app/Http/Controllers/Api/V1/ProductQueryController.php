<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductListCollection;
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
}
