<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderListCollection;
use App\Http\Resources\V1\OrderDetailResource;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        $check = Customer::find($request->user()->id);

        $data = Order::where("customer_id", "=", $request->user()->id)->get();
        $count = $data->count();

        if (empty($count)) {
            return response()->json([
                'success' => false,
                "errors" => "There is no orders"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new OrderListCollection($data)
        ]);
        // return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreOrderRequest $request)
    {
        $customer = Customer::find($request->user()->id);

        // Need to reconsider this IF Condition
        if (empty($customer)) {
            return response()->json([
                "success" => false,
                "errors" => "Customer ID is invalid"
            ]);
        }

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)->get();

        if ($data->count() === 0) {
            return response()->json([
                "success" => false,
                "errors" => "Your cart is empty or Your Order is currently in progress"
            ]);
        }

        $arr = [];
        $total_price = 0;

        for ($i = 0; $i < sizeof($data); $i++) {
            $value = DB::table("products")
                ->where("id", "=", $data[$i]->product_id)
                ->first();

            $arr[$i]['product_id'] = $value->id;
            $arr[$i]['quantity'] = $data[$i]->quantity;
            $arr[$i]['price'] = $value->price;
            $arr[$i]['percent_sale'] = $value->percent_sale;
            $sale_price = $value->price * $value->percent_sale / 100;
            $total_price = $total_price + (($value->price - $sale_price) * $data[$i]->quantity);
        }

        // Check if existence of voucherId
        if ($request->voucher_id === null) {
            $filtered = $request->except("voucherId", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
            $filtered["customer_id"] = $request->user()->id;
            $filtered["total_price"] = $total_price;
        } else {
            $voucher_sale = DB::table("vouchers")
                ->where("id", "=", $request->voucher_id)
                ->first();

            $filtered = $request->except("voucherId", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
            $filtered["customer_id"] = $request->user()->id;
            $filtered["total_price"] = $total_price - (($total_price * $voucher_sale->percent) / 100);
        }

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
        $check = Customer::find($request->user()->id);

        // Check Order isExists
        $query = Order::where("orders.id", "=", $request->id)
            ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent")
            ->where("customer_id", "=", $request->user()->id)
            ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$query->exists() || empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong - Please recheck your Customer ID and Order ID"
            ]);
        }

        $data = $query->first();

        // Create product array
        $pivot_table = Order::find($request->id);

        $data["products"] = $pivot_table->products;

        return response()->json([
            "success" => true,
            "data" => new OrderDetailResource($data)
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
        $customer = Customer::find($request->user()->id);

        $query = Order::where("id", "=", $request->id)
            ->where("customer_id", "=", $customer->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Order ID is invalid"
            ]);
        }

        $order = $query->first();

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
}
