<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Delete\DeleteCustomerRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Requests\Customer\Store\StoreProductToCartRequest;
use App\Http\Requests\Customer\Update\UpdateProductToCartRequest;
use App\Http\Resources\V1\CartViewResource;
use App\Http\Resources\V1\CustomerOverviewCollection;
use App\Models\Category;
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
    public function paginator($arr, $request)
    {
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
    public function generateProductsArray(GetCustomerBasicRequest $request)
    {
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
            $arr[$i]['percentSale'] = $products_in_cart[$i]['percent_sale'];
            $arr[$i]['img'] = $products_in_cart[$i]['img'];
            $arr[$i]['quantity'] = $products_in_cart[$i]['pivot']->quantity;
            $arr[$i]['status'] = $products_in_cart[$i]['status'];
            $arr[$i]['deletedAt'] = $products_in_cart[$i]['deleted_at'];
            $categories = DB::table("category_product")
                ->where("product_id", "=", $products_in_cart[$i]['id'])
                ->get();

            for ($j = 0; $j < sizeof($categories); $j++) {
                $category = Category::where("id", "=", $categories[$j]->category_id)
                    ->first();

                $arr[$i]['categories'][$j]['id'] = $category->id;
                $arr[$i]['categories'][$j]['name'] = $category->name;
            }
            // $arr[$i]['categories'] = 
        }

        // return $customer;
        return $arr;
    }

    public function index(GetCustomerBasicRequest $request)
    {
        $check = DB::table("customer_product_cart")
            ->where("customer_id", "=", $request->user()->id)->exists();

        // If cart is empty
        if (!$check) {
            return response()->json([
                "success" => false,
                "errors" => "Giỏ hàng hiện đang trống."
            ]);
        }

        $arr = $this->generateProductsArray($request);

        // if state is "all" then return all
        if ($request->state === "all") {
            return [
                "total" => sizeof($arr),
                "data" => $arr
            ];
        }

        $new_arr = $this->paginator($arr, $request);
        return [
            "total" => sizeof($arr),
            "data" => $new_arr
        ];
    }

    /** CUSTOMER FUNCTION */
    public function store(StoreProductToCartRequest $request)
    {
        if ($request->quantity < 0) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể thêm sản phẩm với số lượng là số âm."
            ]);
        }

        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->product_id);

        if (empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Sản phẩm."
            ]);
        }

        if ($product->quantity < $request->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Số lượng sản phẩm đã gần hết, vui lòng giảm số lượng sản phẩm trước khi thêm vào giỏ hàng."
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
                "message" => "Thêm sản phẩm vào giỏ hàng thành công."
            ]);
        } else {
            $data = $data->where("product_id", "=", $request->product_id)->first();

            $total = $data->quantity + $request->quantity;
            if ($total > $product->quantity) {
                return response()->json([
                    "success" => false,
                    "errors" => "Tổng số lượng sản phẩm đã đến giới hạn, vui lòng giảm số lượng sản phẩm."
                ]);
            }

            $result = $customer->customer_product_cart()->updateExistingPivot($product, [
                "quantity" => $total
            ]);

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Cập nhật thành công số lượng Sản phẩm có ID = " . $product->id
            ]);
        }
    }

    // 1 quantity at a the time for each product
    public function singleQuantity(GetCustomerBasicRequest $request)
    {
        if ($request->quantity < 0) {
            return response()->json([
                "success" => false,
                "errors" => "Số lượng sản phẩm không hợp lệ."
            ]);
        }

        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->id);

        if (empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Sản phẩm."
            ]);
        }

        $data = DB::table("customer_product_cart")->where("customer_id", "=", $customer->id);

        $check = $data->where("product_id", "=", $product->id)->exists();

        // Check total quantity of product has exceeded quantity limit (10 quantity per product in cart)
        if ($check) {
            $productQuantity = $data->where("product_id", "=", $product->id)->first();

            if ($productQuantity->quantity >= 10) {
                return response()->json([
                    "success" => false,
                    "message" => "Một sản phẩm trong giỏ hàng chỉ được thêm tối đa 10 số lượng trên 1 sản phẩm."
                ]);
            }
        }

        if ($product->quantity < $request->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Số lượng sản phẩm đã gần hết, vui lòng giảm số lượng sản phẩm trước khi thêm vào giỏ hàng."
            ]);
        }

        if (empty($check)) {
            $customer->customer_product_cart()->attach($product, [
                "quantity" => 1
            ]);

            return response()->json([
                "success" => true,
                "message" => "Thêm sản phẩm vào giỏ hàng thành công."
            ]);
        } else {
            $data = $data->where("product_id", "=", $request->id)->first();

            $total = $data->quantity + 1;
            if ($total > $product->quantity) {
                return response()->json([
                    "success" => false,
                    "errors" => "Tổng số lượng sản phẩm đã đến giới hạn, vui lòng giảm số lượng sản phẩm."
                ]);
            }

            $result = $customer->customer_product_cart()->updateExistingPivot($product, [
                "quantity" => $total
            ]);

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Cập nhật thành công số lượng Sản phẩm có ID = " . $product->id
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
                "errors" => "Sản phẩm không tồn tại."
            ]);
        }

        $query = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id);

        $check = $query->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Khách hàng và ID Sản phẩm."
            ]);
        }

        $data = $query->first();

        if ($data->quantity === 1) {
            $customer->customer_product_cart()->detach($product);

            return response()->json([
                "success" => true,
                "message" => "Xóa thành công Sản phẩm có ID = " . $request->id . " khỏi giỏ hàng."
            ]);
        }

        $customer->customer_product_cart()->updateExistingPivot($product, [
            "quantity" => $data->quantity - 1
        ]);

        return response()->json([
            "success" => true,
            "message" => "Sản phẩm có ID = " . $request->id . " đã được giảm đi 1 đơn vị số lượng sản phẩm."
        ]);
    }

    public function update(UpdateProductToCartRequest $request)
    {
        $customer = Customer::find($request->user()->id);

        $product = Product::find($request->product_id);

        if (empty($customer) || empty($product)) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Khách hàng và ID Sản phẩm."
            ]);
        }

        // Check Request Quantity before update quantity value to cart
        if ($product->quantity < $request->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Số lượng sản phẩm đã gần hết, vui lòng giảm số lượng sản phẩm trước khi thêm vào giỏ hàng."
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
                    "errors" => "Không thể thêm sản phẩm vào giỏ hàng với số lượng là số âm."
                ]);
            }

            $customer->customer_product_cart()->attach($product, [
                "quantity" => $request->quantity
            ]);

            return response()->json([
                "success" => true,
                "message" => "Sản phẩm có ID = " . $request->product_id . " đã được thêm vào giỏ hàng với số lượng sản phẩm là " . $request->quantity
            ]);
        }

        $data = $query->first();

        // If $request->quantity value is negative
        if ($data->quantity <= ($request->quantity * -1)) { // **$request->quantity * -1** use for checking negative number
            $customer->customer_product_cart()->detach($product);

            return response()->json([
                "success" => true,
                "message" => "Xóa thành Sản phẩm có ID = " . $request->id . " khỏi giỏ hàng."
            ]);
        }

        // Check current total quantity product before add
        $total = $data->quantity + $request->quantity;
        if ($total > $product->quantity) {
            return response()->json([
                "success" => false,
                "errors" => "Tổng số lượng sản phẩm đã đến giới hạn, vui lòng giảm số lượng sản phẩm."
            ]);
        }

        $customer->customer_product_cart()->updateExistingPivot($product, [
            "quantity" => $total
        ]);

        if ($request->quantity < 0) {
            return response()->json([
                "success" => true,
                "message" => "Sản phẩm vói ID = " . $request->product_id . " đã được giảm thành công với số lượng là " . $request->quantity * (-1)
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công số lượng sản phẩm là " . $request->quantity . " cho một sản phẩm trong giỏ hàng."
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
                "errors" => "Vui lòng kiểm tra lại ID Sản phẩm và ID Khách hàng."
            ]);
        }

        $check = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $request->id)
            ->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Sản phẩm có thể không tồn tại trong giỏ hàng của Khách hàng. Vui lòng kiểm tra lại ID Sản phẩm và ID Khách hàng."
            ]);
        }

        $customer->customer_product_cart()->detach($product);

        return response()->json([
            "success" => true,
            "message" => "Sản phẩm có ID = " . $request->id . " đã được xóa khỏi giỏ hàng."
        ]);
    }

    public function empty(GetCustomerBasicRequest $request)
    {
        $products_in_cart = DB::table("customer_product_cart")
            ->where("customer_id", "=", $request->user()->id)
            ->get()
            ->count();

        if ($products_in_cart === 0) {
            return response()->json([
                "success" => false,
                "messasge" => "Giỏ hàng hiện đang trống."
            ]);
        }

        $customer = Customer::find($request->user()->id);

        $customer->customer_product_cart()->detach();

        return response()->json([
            "success" => true,
            "message" => "Làm trống giỏ hàng thành công."
        ]);
    }
}
