<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductToCartRequest;
use App\Http\Requests\UpdateProductToCartRequest;
use App\Http\Resources\V1\CartViewResource;
use App\Http\Resources\V1\CustomerOverviewCollection;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // NEED TO RECONSIDER ADDING "CREATED_AT" & "UDPATED_ATT" COLUMN TO TABLE
    // REASON FOR CHECKING USER ACTIVITIES TO MAKE A DECISION TO FREE UP SPACE IN DATABASE VIA PIVOT CART TABLE

    /** Admin FUNCTION */
    public function all()
    {
        // Because we use customer ID as Cart ID so it makes sense that we reuse the other resource api view from other controller
        $customers = Customer::paginate(10);

        return new CustomerOverviewCollection($customers);
    }

    /** Admin & CUSTOMER FUNCTION */
    public function index(Request $request)
    {
        // if state is 0, then it's viewed from Customer POV
        if ((int) $request->state === 0) {
            $user = $request->user()->id;
        }
        // If state is not 0 (it's 1), then it's viewed from Admin POV
        else {
            $user = $request->id;
        }

        $check = DB::table("customer_product_cart")
            ->where("customer_id", "=", $user)->exists();

        if (!$check) {
            return response()->json([
                "success" => false,
                "errors" => "This user hasn't added any product to cart yet"
            ]);
        }

        $customer = Customer::where("id", "=", $user)->first();

        $customer['products'] = $customer->customer_product_cart;

        return response()->json([
            "data" => new CartViewResource($customer)
        ]);
    }

    public function removedProduct(Request $request) {
        $customer = Customer::where("id", "=", $request->id);
        $product = Product::where("id", "=", $request->productId);

        if (!$customer->exists() || !$product->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck Customer ID and Product ID"
            ]);
        }

        $product_cart = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->first()->id)
            ->where("product_id", "=", $product->first()->id);

        // Check emptiness of Customer cart
        if (!$product_cart->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't find product in Customer Cart, product may already got deleted"
            ]);
        }

        $delete = $product_cart->delete();

        // if (empty($delete) || empty($detach)) {
        if (empty($delete)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => false,
            "messagee" => "Removed Product with ID = " . $product->first()->id ." from Customer Cart with ID = " . $customer->first()->id . " successfully"
        ]);
    }

    public function emptyCart(Request $request)
    {
        $customer = Customer::where("id", "=", $request->id);

        if (!$customer->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't empty Customer Cart with invalid Customer ID"
            ]);
        }

        $cart_customer = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->first()->id);

        // Check emptiness of Customer cart
        if (!$cart_customer->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't empty an Empty Cart"
            ]);
        }

        $delete = $cart_customer->delete();

        // if (empty($delete) || empty($detach)) {
        if (empty($delete)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => false,
            "messagee" => "Emptied Customer Cart with ID = " . $customer->first()->id . " successfully"
        ]);
    }
    /** END OF ADMIN FUNCTION */

    /** CUSTOMER FUNCTION */
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

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Customer ID and Product ID"
            ]);
        }

        $data = DB::table("customer_product_cart")->where("customer_id", "=", $customer->id);

        $check = $data->where("product_id", "=", $product->id)->exists();

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

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Customer ID and Product ID"
            ]);
        }

        $query = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id);

        $check = $query->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Product ID"
            ]);
        }

        $data = $query->first();

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
        // If state is 0, then check Customer (from customer site)
        if ((int) $request->state === 0){
            $customer = Customer::find($request->user()->id);
        }
        // If state is 1, then check Customer (from Admin site)
        else {
            $customer = Customer::find($request->id);
        }

        $product = Product::find($request->product_id);

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Customer ID and Product ID"
            ]);
        }

        $query = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id);

        $check = $query->exists();

        if (!$check) {
            if ($request->quantity < 0) {
                return response()->json([
                    "success" => false,
                    "errors" => "Can't add product to cart with negative quantity"
                ]);
            }

            $customer->customer_product_cart()->attach($product,[
                "quantity" => $request->quantity
            ]);

            return response()->json([
                "success" => true,
                "message" => "Product with ID = " . $request->product_id . " has successfully been added with " . $request->quantity . " quantity"
            ]);
        }

        $data = $query->first();

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

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Customer ID and Product ID"
            ]);
        }

        $check = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $request->id)
            ->exists();

        if (empty($check)) {
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
