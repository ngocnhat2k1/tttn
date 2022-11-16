<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Delete\DeleteCustomerRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Requests\Customer\Store\StoreProductToCartRequest;
use App\Http\Requests\Customer\Update\UpdateProductToCartRequest;
use App\Http\Resources\V1\CartViewResource;
use App\Http\Resources\V1\CustomerOverviewCollection;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // NEED TO RECONSIDER ADDING "CREATED_AT" & "UDPATED_ATT" COLUMN TO TABLE
    // REASON FOR CHECKING USER ACTIVITIES TO MAKE A DECISION TO FREE UP SPACE IN DATABASE VIA PIVOT CART TABLE

    // Paginator function
    public function paginator($arr, $request) {
        $total = count($arr);
        $per_page = 5;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    /** Admin & CUSTOMER FUNCTION */
    public function index(GetCustomerBasicRequest $request)
    {
        $check = DB::table("customer_product_cart")
            ->where("customer_id", "=", $request->user()->id)->exists();

        if (!$check) {
            return response()->json([
                "success" => false,
                "errors" => "This user hasn't added any product to cart yet"
            ]);
        }

        $customer = Customer::where("id", "=", $request->user()->id)->first();
        // $customer['products'] = $customer->customer_product_cart;
        $products_in_cart = $customer->customer_product_cart;

        $arr = [];
        // $arr['customer_id'] = $customer->id;

        for ($i = 0; $i < sizeof($products_in_cart); $i++) {
            $arr[$i]['id'] = $products_in_cart[$i]['id']; 
            $arr[$i]['name'] = $products_in_cart[$i]['name'];
            $arr[$i]['description'] = $products_in_cart[$i]['description'];
            $arr[$i]['price'] = $products_in_cart[$i]['price'];
            $arr[$i]['percentSale'] = $products_in_cart[$i]['percentSale'];
            $arr[$i]['img'] = $products_in_cart[$i]['img'];
            $arr[$i]['quantity'] = $products_in_cart[$i]['quantity'];
            $arr[$i]['status'] = $products_in_cart[$i]['status'];
            $arr[$i]['deletedAt'] = $products_in_cart[$i]['deleted_at'];
        }

        // return $customer;
        $new_arr = $this->paginator($arr, $request);
        return $new_arr;
    }


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

        if ($product->quantity < $request->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Out of Product Quantity, please reduce the amount of quantity before add product to cart"
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

            $total = $data->quantity + $request->quantity;
            if ($total > $product->quantity) {
                return response()->json([
                    "success" => false,
                    "errors" => "Total Product Quantity has reached limit, please reduce product quantity"
                ]);
            }

            $result = $customer->customer_product_cart()->updateExistingPivot($product, [
                "quantity" => $total
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

    public function reduce(GetCustomerBasicRequest $request)
    {
        // request->id is Product ID
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Product ID is invalid"
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
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->product_id);

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Customer ID and Product ID"
            ]);
        }
        
        // Check Request Quantity before update quantity value to cart
        if ($product->quantity < $request->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Out of Product Quantity, please reduce the amount of quantity before add product to cart"
            ]);
        }

        $query = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id);

        // If customer hasn't added this product to cart yet, then add it
        /** THIF CHECK CREATE FOR ADMIN TO USE */
        if (!$query->exists()) {
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

        // Check current total quantity product before add
        $total = $data->quantity + $request->quantity;
        if ($total > $product->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Total Product Quantity has reached limit, please reduce product quantity"
            ]);
        }

        $customer->customer_product_cart()->updateExistingPivot($product, [
            "quantity" => $total
        ]);

        if ($request->quantity < 0) {
            return response()->json([
                "success" => true,
                "message" => "Product with ID = " . $request->product_id . " has successfully been reduced " . $request->quantity*(-1) . " quantity"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated " . $request->quantity . " quantity to existed product successfully"
        ]);
    }

    public function destroy(DeleteCustomerRequest $request)
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
