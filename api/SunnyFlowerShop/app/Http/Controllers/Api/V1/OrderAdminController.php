<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Admin\Update\UpdateOrderCustomerStatus;
use App\Http\Resources\V1\CustomerOverviewResource;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\VoucherDetailResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderAdminController extends Controller
{
    /** ADMIN FUNCTIONs */
    public function paginator($arr, $request) {
        $total = count($arr);
        $per_page = 12;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function index(GetAdminBasicRequest $request)
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
                $arr[$index]['customerId'] = $customers_orders[$i]->id;
                $arr[$index]['orderId'] = $customers_orders[$i]->orders[$j]->id;
                $arr[$index]['firstName'] = $customers_orders[$i]->first_name;
                $arr[$index]['lastName'] = $customers_orders[$i]->last_name;
                // $arr[$index]['disabled'] = $customers_orders[$i]->disabled;
                $arr[$index]['voucherId'] = $customers_orders[$i]->orders[$j]->voucher_id;
                $arr[$index]['idDelivery'] = $customers_orders[$i]->orders[$j]->id_delivery;
                $arr[$index]['address'] = $customers_orders[$i]->orders[$j]->address;
                $arr[$index]['nameReceiver'] = $customers_orders[$i]->orders[$j]->name_receiver;
                $arr[$index]['phoneReceiver'] = $customers_orders[$i]->orders[$j]->phone_receiver;
                $arr[$index]['price'] = $customers_orders[$i]->orders[$j]->total_price;
                $arr[$index]['status'] = $customers_orders[$i]->orders[$j]->status;
                $arr[$index]['createdAt'] = date_format($customers_orders[$i]->orders[$j]->created_at, "d/m/Y H:i:s");
                $arr[$index]['updatedAt'] = date_format($customers_orders[$i]->orders[$j]->updated_at, "d/m/Y H:i:s");
                $arr[$index]['deletedBy'] = $customers_orders[$i]->orders[$j]->deleted_by;

                $index++; // index for array we currently use
            }
        }

        $new_arr = $this->paginator($arr, $request);

        return $new_arr;

        // return new OrderCustomerListCollection($customers_orders);
    }

    public function show(GetAdminBasicRequest $request, Order $order)
    {
        // Check existence of Customer and Order via Customer ID and Order ID
        $order["products"] = $order->products;

        $voucher_query = Voucher::where("id", "=", $order->voucher_id);
        $customer_query = Customer::where("id", "=", $order->customer_id);
        
        if (!$voucher_query->exists() || !$customer_query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Order has some invalid information, please double check database before displaying"
            ]);
        }

        $order['voucher'] = $voucher_query->first();
        $order['customer'] = $customer_query->first();

        return response()->json([
            "success" => true,
            "data" => [
                "order" => [
                    "customer" => new CustomerOverviewResource($order->customer),
                    "voucher" => new VoucherDetailResource($order->voucher), 
                    "orderId" => $order->id,
                    "idDelivery" => $order->id_delivery,
                    "dateOrder" => $order->date_order,
                    "address" => $order->address,
                    "nameReceiver" => $order->name_receiver,
                    "phoneReceiver" => $order->phone_receiver,
                    "totalPrice" => $order->total_price,
                    "status" => $order->status,
                    "paidType" => $order->paid_type,
                    "deletedBy" => $order->deleted_by,
                    "products" => ProductDetailResource::collection($order->products)
                ]
            ]
        ]);

        // "products" => ProductDetailResource::collection($this->products)

        // return response()->json([
        //     "success" => true,
            // "data" => new OrderDetailResource($order)
        // ]);
    }

    public function updateStatus(UpdateOrderCustomerStatus $request, Order $order)
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

        $state = (int) $request->state;
        $order->status = $state;
        $result = $order->save();

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

    public function destroy(Order $order, DeleteAdminBasicRequest $request)
    {
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
                    'message' => "Sucessfully cancelled Order ID = " . $order->id
                ]
            );

            // If value state is not 1, it will be Reverse Delete
        } else {

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
                    'message' => "Sucessfully reversed cancel Order ID = " . $order->id
                ]
            );
        }
    }
}
