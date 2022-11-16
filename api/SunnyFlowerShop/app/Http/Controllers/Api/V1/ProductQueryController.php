<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductDetailResource;
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
            ->take(8);

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
        $orders = Order::orderBy("created_at", "ASC")->take(100)->get();

        // Array used to store 100 orders ordered by "created_at" in ascending
        $arr_orders = [];
        $arr_orders_2 = [];
        $current_date = date("Y-m-d H:i:s");
        $index = 0;

        // Get all recent orders meet the require days (base on {day} calculated on second)
        for ($i = 0; $i < sizeof($orders); $i++) {
            if ((strtotime($current_date) - strtotime($orders[$i]['created_at'])) >= ((int) $request->day) * 60 * 60 * 24) {
                // $arr_orders[] = $orders[$i]['id'];
                $arr_orders[] = $orders[$i]['id'];
                $arr_orders_2[] = strtotime($current_date) - strtotime($orders[$i]['created_at']);
                $index++;
            }
        }

        $arr_products = [];
        // Get all products store in order
        for ($i = 0; $i < sizeof($arr_orders); $i++) {
            // dd($arr_orders[$i]);
            $products_in_order = DB::table("order_product")
                ->select("product_id")
                ->where("order_id", "=", $arr_orders[$i])
                ->get();

            if ($products_in_order->count() === 0) {
                // return false and quit
                continue;
            }

            if (sizeof($arr_products) === 0) { // Run first time
                for ($j = 0; $j < sizeof($products_in_order); $j++) {
                    $arr_products[] = $products_in_order[$j]->product_id;
                }
                continue;
            }

            for ($k = 0; $k < sizeof($products_in_order); $k++) {
                // dd($products_in_order[$k]);
                if (!in_array($products_in_order[$k]->product_id, $arr_products)) {
                    $arr_products[] = $products_in_order[$k]->product_id;
                }
            }
        }

        $arr_products_filter = [];
        $index = 0;

        // Check if array is below 10 then use it without product filter
        if (sizeof($arr_products) <= 10) {
            for ($i = 0; $i < sizeof($arr_products); $i++) {
                $product = Product::where("id", "=", $arr_products[$i])->first();

                if ($product->status === 0 || $product->deleted_at !== null) continue;

                $arr_products_filter[$index]['id'] = $product->id;
                $arr_products_filter[$index]['name'] = $product->name;
                $arr_products_filter[$index]['description'] = $product->description;
                $arr_products_filter[$index]['price'] = $product->price;
                $arr_products_filter[$index]['img'] = $product->img;
                $arr_products_filter[$index]['percentSale'] = $product->percent_sale;
                $arr_products_filter[$index]['quantity'] = $product->quantity;
                $index++;
            }

            return $arr_products_filter;
        }

        for ($i = 0; $i < sizeof($arr_products); $i++) {
            $products_filter = DB::table("order_product")
                ->select("product_id", DB::raw('count(product_id) as count'))
                ->groupBy('product_id')
                ->where("product_id", "=", $arr_products[$i])
                ->orderBy('count', 'DESC')
                ->get();

            if ($products_filter[0]->count >= 2) {
                $product = Product::where("id", "=", $products_filter[0]->product_id)->first();

                if ($product->status === 0 || $product->deleted_at !== null) continue;

                $arr_products_filter[$index]['id'] = $product->id;
                $arr_products_filter[$index]['name'] = $product->name;
                $arr_products_filter[$index]['description'] = $product->description;
                $arr_products_filter[$index]['price'] = $product->price;
                $arr_products_filter[$index]['img'] = $product->img;
                $arr_products_filter[$index]['percentSale'] = $product->percent_sale;
                $arr_products_filter[$index]['quantity'] = $product->quantity;
                $index++;
            }
        }

        // If after product filter there still no product then reuse on sale product function
        if (sizeof($arr_products_filter) === 0) {
            $this->sale();
        }

        // If everything went well then return result
        return $arr_products_filter;
    }

    // Display on main page (when login into website)
    public function index(Request $request)
    {
        // $data = Product::with("categories")->paginate();
        $data = Product::with("categories");
        $count = $data->get()->count();

        if (empty($count)) {
            return response()->json([
                "success" => false,
                "errors" => "Product list is empty"
            ]);
        }

        // Will change later, this is just temporary
        if (!empty($request->get("q"))) {
            $check = (int)$request->get("q");
            $column = "";
            $operator = "";
            $value = "";

            if ($check == 0) {
                $column = "name";
                $operator = "like";
                $value = "%" . $request->get("q") . "%";
            } else {
                $column = "id";
                $operator = "=";
                $value = $request->get("q");
            }

            $search = Product::where("$column", "$operator", "$value")->get();
        }

        $count = DB::table("products")->count();

        // return response()->json([
        //     "success" => true,
        //     "total" => $count,
        //     "data" => new ProductListCollection($data)
        // ]);

        return new ProductListCollection($data->paginate(8)->appends($request->query()));
    }

    public function show(Request $request)
    {
        $data = Product::find($request->id);

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Product doesn't not exist"
            ]);
        }

        $average_quality = DB::table("customer_product_feedback")
            ->where("product_id", "=", $data->id);

        // calculate average of total quality that product has
        $quality = 0;

        /** Checking if quality of feedback has been made */
        // If not then average of total quality is 0
        if (!$average_quality->exists()) {
            $quality = 0;
        }
        // If so then calculate it
        else {
            $total = $average_quality->get(); // Get all quality feedback

            for ($i = 0; $i < sizeof($total); $i++) { // Sum all quality to make an average calculation
                $quality += $total[$i]->quality;
            }

            $quality = $quality / sizeof($total);

            $float_point = explode(".", $quality);

            if (sizeof($float_point) >= 2) {
                $decimal_number = (int)$float_point[1];

                while ($decimal_number > 10) {
                    $decimal_number = $decimal_number / 10;
                }

                if ($decimal_number >= 5) {
                    $quality = ceil($quality);
                } else {
                    $quality = floor($quality);
                }
            }
        }

        $data['quality'] = $quality;

        return response()->json([
            "success" => true,
            "data" => new ProductDetailResource($data)
        ]);
    }
}
