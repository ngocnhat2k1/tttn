<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderListCollection;
use App\Http\Resources\V1\OrderDetailResource;
use App\Http\Resources\V1\ProductDetailResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $data = Order::where("customer_id", "=", $request->user()->id);
        $count = $data->get()->count();

        if (empty($count)) {
            return response()->json([
                'success' => false,
                "errors" => "There is no orders"
            ]);
        }

        return new OrderListCollection($data->paginate(12));

        // return response()->json([
        //     "success" => true,
        //     "data" => new OrderListCollection($data->paginate(10))
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreOrderRequest $request)
    {
        /** ##### IF CONDITION SECTION ##### */
        $customer = Customer::find($request->user()->id); // Later use for detach value from intermediate table

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)->get();

        if ($data->count() === 0) {
            return response()->json([
                "success" => false,
                "errors" => "Your cart is empty or Your Order is currently in progress"
            ]);
        }

        // Set to Vietnam timezone
        // date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Check voucher existence
        // $query = Voucher::where("id", "=", $request->voucher_id);
        $query = Voucher::where("name", "=", $request->voucher_code);

        // If voucher is existed, then continue checking voucher attributes
        if ($query->exists()) {

            $vouchers = $query->first();

            // Check expired date and "Deleted" Attributes
            $current_date = date("Y-m-d H:i:s");

            if ((strtotime($vouchers->expired_date) - strtotime($current_date)) < 0 || $vouchers->deleted !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Voucher code is expired, please recheck your voucher code"
                ]);
            }

            // Check usage
            if ($vouchers->usage === 0) {
                return response()->json([
                    "success" => false,
                    "errors" => "Voucher code is out of usage, better luck next time"
                ]);
            }

            // Check Customer has already used vouher (?)
            $voucher_exist_in_customer = Order::where("voucher_id", "=", $vouchers->id)
                ->where("customer_id", "=", $request->user()->id)->exists();

            if ($voucher_exist_in_customer) {
                return response()->json([
                    "success" => false,
                    "errors" => "You have already used this voucher."
                ]);
            }
        } else if (!empty($request->voucher_code)) {
            return response()->json([
                "success" => false,
                "errors" => "Voucher code doesn't exist, please recheck your voucher code"
            ]);
        }

        // Check product is available or already got deleted
        $count = 0;
        $invalid_product_quantity_arr = [];
        $index = 0; // use for invalid_quatity_arr array
        for ($i = 0; $i < sizeof($data); $i++) {
            $value = Product::where("id", "=", $data[$i]->product_id)->first();

            // Comparing Quantity product from Cart is "smaller than" Total remaining Product currently has
            if ($data[$i]->quantity > $value->quantity) {
                $invalid_product_quantity_arr[$index] = $value->name;
                $index++;
            }

            if ($value->status === 0 || $value->deleted_at !== null) {
                $count++;
            }
        }

        // Check if Current Customer Cart has any invalid Product Quantity
        if (!empty($invalid_product_quantity_arr)) {
            return response()->json([
                "success" => false,
                "errors" => "Some products don't have enough quantity in Cart. These are: " . implode(", ", $invalid_product_quantity_arr)
            ]);
        }

        // if count !== 0, that mean 1 or 2 product is got deleted or already out of stock
        if ($count !== 0) {
            return response()->json([
                "success" => false,
                "errors" => "1 or 2 product got deleted or have already out of stock, please recheck your cart"
            ]);
        }
        /** ##### END OF IF CONDITION SECTION ##### */

        $arr = [];
        $total_price = 0;
        $voucher_data = $query->first();
        $voucher_sale_value = $voucher_data->percent ?? 0;

        for ($i = 0; $i < sizeof($data); $i++) {
            $value = Product::where("id", "=", $data[$i]->product_id)->first();

            $arr[$i]['product_id'] = $value->id;
            $arr[$i]['quantity'] = $data[$i]->quantity;
            $arr[$i]['price'] = $value->price;
            $arr[$i]['percent_sale'] = $value->percent_sale;
            $sale_price = $value->price * $value->percent_sale / 100;

            $total_price = $total_price + (($value->price - $sale_price) * $data[$i]->quantity);
        }

        $filtered = $request->except("voucherCode", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
        $filtered['voucher_id'] = $voucher_data->id ?? null;
        $filtered["customer_id"] = $request->user()->id;
        $filtered["total_price"] = $total_price - (($total_price * $voucher_sale_value) / 100);

        // Change usage value of voucher, But first need to check VoucherCode field
        if (!empty($request->voucher_code)) {
            if ($voucher_data->usage === 1) { // If voucher usage is = 1, then change its value to 0 and change deleted value
                $voucher_data->usage = 0;
                $voucher_data->deleted = 1;
                $voucher_data->save();
            } else { // If voucher usage is not = 0, then descrease like normal
                $voucher_data->usage = $voucher_data->usage - 1;
                $voucher_data->save();
            }
        }

        // Add order to database
        $check = Order::create($filtered);

        // Check if data insert to database isSuccess
        if (empty($check->id)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        $order = Order::find($check->id);

        // Insert data into intermediate table (order_product)
        for ($i = 0; $i < sizeof($arr); $i++) {
            $productId = Product::find($arr[$i]["product_id"]);
            $confirm = $order->products()->attach($productId, [
                "quantity" => $arr[$i]["quantity"],
                "price" => $arr[$i]["price"],
                "percent_sale" => $arr[$i]['percent_sale']
            ]);
        }

        // Detach data from intermediate table (customer_product_cart)
        $detach = $customer->customer_product_cart()->detach();

        if (empty($detach)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Placed order successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */

    // Can't check order id is existed in database for some
    public function show(Request $request)
    {
        // $check = Customer::find($request->user()->id);

        // Check Order isExists
        $query = Order::where("orders.id", "=", $request->id)
            ->addSelect(
                "orders.*",
                "vouchers.id as voucher_id",
                "vouchers.name",
                "vouchers.percent",
                "vouchers.expired_date",
                "vouchers.deleted"
            )
            ->where("customer_id", "=", $request->user()->id)
            ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, Please recheck Order ID"
            ]);
        }

        $data = $query->first();

        // Create product array
        $pivot_table = Order::find($request->id);

        $data["products"] = $pivot_table->products;

        return response()->json([
            "success" => true,
            "data" => [
                "customer" => [
                    "customerId" => $request->user()->id,
                    "firstName" => $request->user()->first_name,
                    "lastName" => $request->user()->last_name,
                    "email" => $request->user()->email,
                    "avatar" => $request->user()->avatar,
                    "defaultAvatar" => $request->user()->default_avatar,
                ],
                "voucher" => [
                    "voucherId" => $data->voucher_id,
                    "percent" => $data->percent,
                    "name" => $data->name,
                    "expiredDate" => $data->expired_date,
                    "deleted" => $data->deleted,
                ],
                "order" => [
                    "id" => $data->id,
                    "idDelivery" => $data->id_delivery,
                    "dateOrder" => $data->date_order,
                    "address" => $data->address,
                    "nameReceiver" => $data->name_receiver,
                    "phoneReceiver" => $data->phone_receiver,
                    "totalPrice" => $data->total_price,
                    "status" => $data->status,
                    "paidType" => $data->paid_type,
                    "deleted_by" => $data->deleted_by,
                    "createdAt" => date_format($data->created_at, "Y-m-d H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "Y-m-d H:i:s"),
                ],
                "products" => ProductDetailResource::collection($data->products)
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $customer = Customer::find($request->user()->id);

        $query = Order::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Order ID is invalid"
            ]);
        }

        $order = $query->first();

        if ($order->deleted_by !== null) {
            return response()->json([
                "success" => false,
                "errors" => "Order has already cancelled"
            ]);
        }

        // This function cancel by customer so value will be 0
        $order->deleted_by = 0;

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
                'errors' => "Sucessfully canceled Order ID = " . $request->id
            ]
        );
    }

    public function updateStatus(Request $request)
    {
        $query = Order::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);

        // Check connection between Customer and Order
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer don't have this Order with ID = " . $request->id
            ]);
        }

        $order = $query->first();

        // We only allow customer to change Order Status to Completed state
        if ($order->status === 0 || $order->status === 2) {
            return response()->json([
                "success" => false,
                "errors" => "This Order was updated to Completed State"
            ]);
        }

        $products = DB::table("order_product")
            ->where("order_id", "=", $order->id)->get();

        if ($products->count() === 0) {
            return response()->json([
                "success" => false,
                "errors" => "HOW AN ORDER DON'T HAVE ANY PRODUCTS??? WHAT IS THIS???"
            ]);
        }

        $order->status = 2;
        // $order_detach = Order::find($request->id); // Create this to only

        // Descrease Product Quantity
        for ($i = 0; $i < sizeof($products); $i++) {
            $product = Product::find($products[$i]->product_id);

            $remain = $product->quantity - $products[$i]->quantity; // Remain quantity after decrease
            if ($remain <= 0) { // If Remain Quantity is less equal than 0 then set it to out of stock
                $product->quantity = 0;
                $product->status = 0;
            } else { // If not then nope
                $product->quantity = $remain;
            }

            $product->save();
        }

        $result = $order->save();

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected errors has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully Updated Order state to Completed state"
        ]);

        /**
         * Save Order new Status
         * Decrease all Product quantity in from Order
         */
    }
}
