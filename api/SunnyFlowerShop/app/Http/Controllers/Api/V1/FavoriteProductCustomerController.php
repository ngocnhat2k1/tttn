<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductFavoriteCustomerOverviewCollection;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteProductCustomerController extends Controller
{
    public function index(Customer $customer)
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

        return new ProductListCollection($customer->customer_product_favorite);
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
