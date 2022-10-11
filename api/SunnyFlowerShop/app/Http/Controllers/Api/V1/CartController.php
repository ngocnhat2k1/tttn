<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductToCartRequest;
use App\Http\Requests\UpdateProductToCartRequest;
use App\Http\Resources\V1\CartViewResource;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // NEED TO RECONSIDER ADDING "CREATED_AT" & "UDPATED_ATT" COLUMN TO TABLE
    // REASON FOR CHECKING USER ACTIVITIES TO MAKE A DECISION TO FREE UP SPACE IN DATABASE VIA PIVOT CART TABLE
    public function index(Request $request)
    {
        $customer = Customer::where("id", "=", $request->user()->id)->first();

        $customer['products'] = $customer->customer_product_cart;

        return response()->json([
            "data" => new CartViewResource($customer)
        ]);
    }

    public function store(StoreProductToCartRequest $request)
    {
        if ($request->quantity < 0) {
            return response()->json([
                "success" => false,
                "errors" => "Quantity value is invalid"
            ]);
        }

        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->product_id);

        $data = DB::table("customer_product_cart")->where("customer_id", "=", $customer->id);

        $check = $data->where("product_id", "=", $product->id)->first();

        if (empty($check)) {
            $customer->customer_product_cart()->attach($product, [
                "quantity" => $request->quantity
            ]);

            return response()->json([
                "success" => true,
                "message" => "Added product to cart successfully"
            ]);
        } else {
            $data = $data->where("product_id", "=", $request->product_id)->first();

            $result = $customer->customer_product_cart()->updateExistingPivot($product, [
                "quantity" => $data->quantity + $request->quantity
            ]);

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Something went wrong"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Updated quantity of existed product successfully"
            ]);
        }
    }

    public function reduce(Request $request)
    {
        // request->id is Product ID
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id)
            ->first();

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Product ID"
            ]);
        }

        if ($data->quantity === 1) {
            $customer->customer_product_cart()->detach($product);

            return response()->json([
                "success" => true,
                "message" => "Successfully removed Product with ID = " . $request->id . " from cart"
            ]);
        }

        $customer->customer_product_cart()->updateExistingPivot($product, [
            "quantity" => $data->quantity - 1
        ]);

        return response()->json([
            "success" => true,
            "message" => "Product with ID = " . $request->id . " has successfully been reduced 1 quantity"
        ]);
    }

    public function update(UpdateProductToCartRequest $request)
    {
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->product_id);

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id)
            ->first();

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Product ID"
            ]);
        }

        // If $request->quantity value is negative
        if ($data->quantity <= ($request->quantity * -1)) { // **$request->quantity * -1** use for checking negative number
            $customer->customer_product_cart()->detach($product);

            return response()->json([
                "success" => true,
                "message" => "Successfully removed Product with ID = " . $request->id . " from cart"
            ]);
        }

        $customer->customer_product_cart()->updateExistingPivot($product, [
            "quantity" => $data->quantity + $request->quantity
        ]);

        if ($request->quantity < 0) {
            return response()->json([
                "success" => true,
                "message" => "Product with ID = " . $request->product_id . " has successfully been reduced " . $request->quantity . " quantity"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated " . $request->quantity . " quantity to existed product successfully"
        ]);
    }

    public function destroy(Request $request)
    {
        // request->id is Product ID
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $request->id)
            ->first();

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Product ID"
            ]);
        }

        $customer->customer_product_cart()->detach($product);

        return response()->json([
            "success" => true,
            "message" => "Proudct with ID = " . $request->id . " has been successfully remomved from cart"
        ]);
    }
}
