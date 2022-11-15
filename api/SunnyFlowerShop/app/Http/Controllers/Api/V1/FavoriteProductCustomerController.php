<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductFavoriteCustomerOverviewCollection;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FavoriteProductCustomerController extends Controller
{
    public function paginator($arr, $request)
    {
        $total = count($arr);
        $per_page = 10;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function index(Customer $customer, Request $request)
    {
        // Check existence of product in favorite section
        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->get()->count();
            
        if ($check === 0) {
            return response()->json([
                "success" => false,
                "message" => "This user hasn't added any product to favorite yet"
            ]);
        }

        $customer_product_favorite = Customer::with("customer_product_favorite")->where("id", "=", $customer->id)->get();

        $data = [];

        // Second loop for Products
        for ($j = 0; $j < sizeof($customer_product_favorite[0]['customer_product_favorite']); $j++) {
            $data[$j]['productId'] = $customer_product_favorite[0]['customer_product_favorite'][$j]->id;
            $data[$j]['productName'] = $customer_product_favorite[0]['customer_product_favorite'][$j]->name;
            $data[$j]['img'] = $customer_product_favorite[0]['customer_product_favorite'][$j]->img;

            // $categories = DB::table("category_product")
            //     ->where("product_id", "=", $customer_product_favorite[0]['customer_product_favorite'][$j]->id)
            //     ->get();

            // for ($k = 0; $k < sizeof($categories); $k++) {
            //     $category = Category::where("id", "=", $categories[$k]->id)->first();
            //     $data[0]['products'][$j]['categories'][$k]['id']= $category->id;
            //     $data[0]['products'][$j]['categories'][$k]['name']= $category->name;
            // }

            $data[$j]['quality'] = $customer_product_favorite[0]['customer_product_favorite'][$j]['pivot']->quality;
            $data[$j]['comment'] = $customer_product_favorite[0]['customer_product_favorite'][$j]['pivot']->comment;
        }

        return $this->paginator($data, $request);

        // return new ProductListCollection($customer->customer_product_favorite);
    }

    public function destroy(Customer $customer, Product $product)
    {
        $product_data = Product::find($product->id);

        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product_data->id)
            ->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Can't remove an unexisted product from favorite"
            ]);
        }

        $data = $customer->customer_product_favorite()->detach($product_data);

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Removed product from favorite successfully"
        ]);
    }
}
