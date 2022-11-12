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
use Illuminate\Pagination\LengthAwarePaginator;

class OrderCustomerController extends Controller
{
    /** ADMIN FUNCTIONs */
    public function all(Request $request)
    {
        $check = Order::get()->count();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Order list is empty"
            ]);
        }

        // $orders = Order::with("customers")->paginate(10);
        $customers_orders = Customer::with("orders")->get();
        // return $customers_orders;
        $arr = [];
        $index = 0;

        // Do two for loop to add all order to array
        for ($i = 0; $i < sizeof($customers_orders); $i++) { // First loop is used to get into Customer index at $i
            // Second loop is used to get Order index at $j from Customer index at $i
            for ($j = 0; $j < sizeof($customers_orders[$i]->orders); $j++) {
                $arr[$index]['orderId'] = $customers_orders[$i]->orders[$j]->id;
                $arr[$index]['firstName'] = $customers_orders[$i]->first_name;
                $arr[$index]['lastName'] = $customers_orders[$i]->last_name;
                // $arr[$index]['disabled'] = $customers_orders[$i]->disabled;
                $arr[$index]['voucherId'] = $customers_orders[$i]->orders[$j]->voucher_id;
                $arr[$index]['id_delivery'] = $customers_orders[$i]->orders[$j]->id_delivery;
                $arr[$index]['address'] = $customers_orders[$i]->orders[$j]->address;
                $arr[$index]['nameReceiver'] = $customers_orders[$i]->orders[$j]->name_receiver;
                $arr[$index]['phoneReceiver'] = $customers_orders[$i]->orders[$j]->phone_receiver;
                $arr[$index]['price'] = $customers_orders[$i]->orders[$j]->total_price;
                $arr[$index]['status'] = $customers_orders[$i]->orders[$j]->status;
                $arr[$index]['createdAt'] = date_format($customers_orders[$i]->orders[$j]->created_at, "Y-m-d H:i:s");
                $arr[$index]['updatedAt'] = date_format($customers_orders[$i]->orders[$j]->updated_at, "Y-m-d H:i:s");
                $arr[$index]['deletedBy'] = $customers_orders[$i]->orders[$j]->deleted_by;

                $index++; // index for array we currently use
            }
        }

        $total = count($arr);
        $per_page = 5;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return ((object) $arr);

        // return new OrderCustomerListCollection($customers_orders);
    }

    /** END OF ADMIN FUNCTIONs */

    public function index(Customer $customer)
    {
        $order = $customer->orders;

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
        $query = Order::where("orders.id", "=", $order->id)
            ->addSelect(
                "orders.*",
                "vouchers.id as voucher_id",
                "vouchers.name",
                "vouchers.percent",
                "vouchers.expired_date",
                "vouchers.deleted"
            )
            ->where("customer_id", "=", $customer->id)
            ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$query->exists()) {
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
        $query = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer don't have this Order with ID = " . $order->id
            ]);
        }

        $order_data = $query->first();

        // Checking Order_status or Deleted_by column isNull
        // 0 fresh new and null is order doesn't get cancelled
        if ($order_data->status === 0 && $order_data->deleted_by === null) {
            // Create products array
            $products = $request->products;

            // Get voucher & create total_price variable
            if ($request->voucher_id !== null) {
                $voucher = Voucher::find($request->voucher_id);
                $voucher_value = $voucher->percent;
            } else {
                $voucher_value = 0;
            }

            $total_price = 0;

            // Delete all previous products in pivot "order_product" table
            $order_data->products()->detach();

            // ReAdd all new products to pivot "order_product" table
            for ($i = 0; $i < sizeof($products); $i++) {

                // Check product ID
                $product = Product::find($products[$i]['id']);
                $order_data->products()->attach($product, [
                    "quantity" => $products[$i]["quantity"],
                    "price" => $product->price,
                    "percent_sale" => $product->percent_sale
                ]);

                // Create variable to store sale price of product
                $sale_price = ($product->price * $product->percent_sale) / 100;
                $total_price = $total_price + (($product->price - $sale_price) * $products[$i]["quantity"]);
            }

            // Calculate total price with voucher
            $filtered = $request->except("voucherId", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
            $filtered["customer_id"] = $customer->id;
            $filtered["total_price"] = $total_price - (($total_price * $voucher_value) / 100);

            $check = $order_data->update($filtered);

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
         * Status can change from "Pending" to "Confirmed" and vice versa if admin dectects any supscious actions
         * Status can only be changed from "Confirmed" to "Completed", no reverse allow
         * When status is in "Completed" status, quantity was store in pivot table "order_prodcut" with use to minus the quantity of products in "products" table
         */

        // Check order (Soft) Delete state and Order status
        if ($order->deleted_by !==  null || $order->status === 2) {
            return response()->json([
                "success" => false,
                "errors" => "Can't Update Status when Order got cancelled or Order is in Completed state"
            ]);
        }

        $query = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        // Check Connection between Customer and Order
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer don't have this Order with ID = " . $order->id
            ]);
        }

        $order_data = $query->first();

        $state = (int) $request->state;
        $order_data->status = $state;
        $result = $order_data->save();

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        if ($state === 0) {
            $order_state = "Pending";
        } else if ($state === 1) {
            $order_state = "Confirmed";
        } else {
            $order_state = "Completed";
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully Updated Order with ID = " . $order->id .  " to " . $order_state . " state"
        ]);
    }

    public function destroy(Customer $customer, Order $order, Request $request)
    {
        // Checking The connection between Order and Customer
        $order_data = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id)
            ->exists();

        if (!$order_data) {
            return response()->json([
                "success" => false,
                "errors" => "Please recheck Order ID and Customer ID"
            ]);
        }

        // If Order state is 2 (Completed state), then return
        if ($order->status === 2) {
            return response()->json([
                "success" => false,
                "errors" => "Can't cancelled Order in Completed State"
            ]);
        }

        // Check state to switch between (Soft) Delete and Reverse Delete
        // If value state is 1, it will be (Soft) Delete
        // State right here is delete state not Order state
        if ((int)$request->state !== 0) {
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
            if (!$order_data) {
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
