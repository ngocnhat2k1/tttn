<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\V1\OrderDetailResource;
use App\Http\Resources\V1\OrderListCollection;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class OrderCustomerController extends Controller
{
    public function all() {
        $check = Order::get()->count();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Order list is empty"
            ]);
        }

        return new OrderListCollection(Order::paginate(10));
    }
    
    public function index(Customer $customer)
    {
        $customer_data = Customer::find($customer->id);

        if (empty($customer_data)) {
            return response()->json([
                "success" => false,
                "errors" => "User ID is invalid"
            ]);
        }

        $order = $customer_data->orders;

        if (empty($order)) {
            return response()->json([
                "success" => false,
                "errors" => "This user currently hasn't placed any order"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new OrderListCollection($order)
        ]);
    }

    public function show(Customer $customer, Order $order)
    {
        // Check existence of Customer and Order via Customer ID and Order ID
        $check = Customer::find($customer->id);

        $query = Order::where("orders.id", "=", $order->id)
            ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent")
            ->where("customer_id", "=", $customer->id)
            ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$query->exists() || empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Order ID"
            ]);
        }

        $data = $query->first();

        // Create product array
        $pivot_table = Order::find($order->id);

        $data["products"] = $pivot_table->products;

        return response()->json([
            "success" => true,
            "data" => new OrderDetailResource($data)
        ]);
    }

    public function update(UpdateOrderRequest $request, Customer $customer, Order $order)
    {
        // Check existence of Order ID
        $order = Order::find($order->id);

        // Checking Order_status or Deleted_by column isNull
        // 0 fresh new and null is order doesn't get cancelled
        if ($order->status === 0 && $order->deleted_by === null) {
            // Create products array
            $products = $request->products;

            // Get voucher & create total_price variable
            $voucher = Voucher::find($request->voucher_id);
            $total_price = 0;

            // Delete all previous products in pivot "order_product" table
            $order->products()->detach();

            // ReAdd all new products to pivot "order_product" table
            for ($i = 0; $i < sizeof($products); $i++) {

                // Check product ID
                $product = Product::find($products[$i]['id']);
                $order->products()->attach($product, [
                    "quantity" => $products[$i]["quantity"],
                    "price" => $product->price,
                    "percent_sale" => $product->percent_sale
                ]);

                // Create variable to store sale price of product
                $sale_price = ($product->price * $product->percent_sale) / 100;
                $total_price = $total_price + (($product->price - $sale_price) * $products[$i]["quantity"]);
            }

            dd($total_price);

            // Calculate total price with voucher
            $filtered = $request->except("voucherId", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
            $filtered["customer_id"] = $customer->id;
            $filtered["total_price"] = $total_price - (($total_price * $voucher->percent) / 100);

            $check = $order->update($filtered);

            // Check if data insert to database isSuccess
            if (empty($check)) {
                return response()->json([
                    "success" => false,
                    "errors" => "Something went wrong"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Updated Order ID = " . $order->id . " successfully"
            ]);
        }

        return response()->json([
            "success" => false,
            "errors" => "Please recheck Order status and Order deleted_by"
        ]);
    }

    public function updateStatus(Request $request, Customer $customer, Order $order)
    {
        /** Update current status of order 
         * Status can only be changed from "In_prodgress" to "Shipping", "Completed" or "Received" Only available for customers (Maybe)
         * When status is in "Shipping" status, quantity was store in pivot table "order_prodcut" with use to minus the quantity of products in "products" table
         */
    }

    public function destroy(Customer $customer, Order $order, Request $request)
    {
        // Checking The connection between Order and Customer
        $order = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id)
            ->first();

        if (empty($order)) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Order ID and Customer ID"
            ]);
        }

        // Check state to switch between (Soft) Delete and Reverse Delete
        // If value state is 1, it will be (Soft) Delete
        if ((int)$request->state === 1) {
            // Checking whether Deleted_by column is null or not
            if ($order->deleted_by !== null || $order->deleted_by === 0) {
                return response()->json([
                    "success" => false,
                    "errors" => "Order with ID = " . $order->id . " has already been cancelled"
                ]);
            }

            // If not then asign value to order->deleted_by by 1 (1 for admin; 0 for customer)
            $order->deleted_by = 1;

            $result = $order->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully cancelled Order ID = " . $order->id . " for Customer ID = " . $customer->id
                ]
            );
        
        // If value state is not 1, it will be Reverse Delete
        } else {
            // Checking The connection between Order and Customer
            $order = Order::where("id", "=", $order->id)
                ->where("customer_id", "=", $customer->id)
                ->first();

            if (empty($order)) {
                return response()->json([
                    "success" => false,
                    "errors" => "Please recheck Order ID and Customer ID"
                ]);
            }

            // Checking whether Deleted_by column is null or not
            if ($order->deleted_by === null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Order with ID = " . $order->id . " has already been reversed cancel"
                ]);
            }

            // If not then asign value to order->deleted_by by 1 (1 for admin; 0 for customer)
            $order->deleted_by = null;

            $result = $order->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully reversed cancel Order ID = " . $order->id . " for Customer ID = " . $customer->id
                ]
            );
        }
    }
}
