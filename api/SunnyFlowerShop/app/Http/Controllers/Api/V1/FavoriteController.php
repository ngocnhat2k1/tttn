<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function viewFavorite(Request $request)
    {
        $customer = Customer::find($request->user()->id);

        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->get()->count();
            
        if ($check === 0) {
            return response()->json([
                "success" => false,
                "message" => "This user hasn't added any product to favorite yet"
            ]);
        }

        return response()->json([
            "success" => true,
            "customerId" => $customer->id,
            "data" => new ProductListCollection($customer->customer_product_favorite)
        ]);
    }

    public function storeFavorite(Request $request)
    {
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id)
            ->exists();

        if (empty($check)) {
            $customer->customer_product_favorite()->attach($product);

            return response()->json([
                "success" => true,
                "message" => "Added Product to favorite successfully"
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Product has already been added to favorite"
        ]);
    }

    public function destroyFavorite(Request $request)
    {
        // $request->id is Product ID
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id)
            ->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Can't remove an unexisted product from favorite"
            ]);
        }

        $data = $customer->customer_product_favorite()->detach($product);

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

    // This function use when product in favorite section is purchased
    public static function isPurchased(Request $request) {
        return self::destroyFavorite($request);
    }
}
