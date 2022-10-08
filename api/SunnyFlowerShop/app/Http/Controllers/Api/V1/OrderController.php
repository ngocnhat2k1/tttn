<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderListCollection;
use App\Http\Resources\V1\OrderDetailResource;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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

        // Will fix this later when i figure out the other way
        if ($check->token == null) {
            return response()->json([
                "success" => false,
                "errors" => "You have no permission here"
            ]);
        }

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
        // $request->
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */

    // Can't check order id is existed in database for some
    public function show(Request $request, Order $order)
    {
        $check = Customer::find($request->user()->id);

        $product_array = [];
        $index = 0;

        // Will fix this later when i figure out the other way
        if ($check->{"token"} == null) {
            return response()->json([
                "success" => false,
                "errors" => "You have no permission here"
            ]);
        }

        // Check Order isExists
        $data = Order::where("orders.id", "=", $order->id)
            ->where("customer_id", "=", $request->user()->id)
            ->join("vouchers", "orders.voucher_id", "=", "vouchers.id")
            ->first();

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Either id customer or id order is invalid"
            ]);
        }

        // Create product array
        $pivot_table = Order::find($order->id);

        $data["products"] = $pivot_table->products;

        return response()->json([
            "success" => true,
            "data" => new OrderDetailResource($data)
        ]);
        // return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
