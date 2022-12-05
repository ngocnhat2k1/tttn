<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Delete\DeleteCustomerRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Resources\V1\OrderListCollection;
use App\Http\Resources\V1\ProductDetailResource;
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

    public function index(GetCustomerBasicRequest $request)
    {
        $data = Order::where("customer_id", "=", $request->user()->id);
        $count = $data->get()->count();

        if (empty($count)) {
            return response()->json([
                'success' => false,
                "errors" => "Danh sách đơn hàng hiện đang trống."
            ]);
        }

        return new OrderListCollection($data->paginate(12));

        // return response()->json([
        //     "success" => true,
        //     "data" => new OrderListCollection($data->paginate(10))
        // ]);
    }

    /** END OF PLACE ORDER FUNCTION */

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */

    // Can't check order id is existed in database for some
    public function show(GetCustomerBasicRequest $request)
    {
        // $check = Customer::find($request->user()->id);

        // Check Order isExists
        $query = Order::where("orders.id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);


        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $data = $query->first();

        if ($data->voucher_id !== null) {
            $voucher = Voucher::where("id", "=", $data->voucher_id)->first();

            $voucher_data = [
                "voucherId" => $voucher->voucher_id,
                "percent" => $voucher->percent,
                "name" => $voucher->name,
                "expiredDate" => $voucher->expired_date,
                "deleted" => $voucher->deleted,
            ];
        } else {
            $voucher_data = null;
        }

        // Create product array
        $pivot_table = $query->first();

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
                "voucher" => $voucher_data,
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
                    "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s"),
                ],
                "products" => ProductDetailResource::collection($data->products)
            ]
        ]);
    }

    public function showViaIdDelivery(GetCustomerBasicRequest $request)
    {
        // $check = Customer::find($request->user()->id);

        // Check Order isExists
        $query = Order::where("orders.id_delivery", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);


        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $data = $query->first();

        if ($data->voucher_id !== null) {
            $voucher = Voucher::where("id", "=", $data->voucher_id)->first();

            $voucher_data = [
                "voucherId" => $voucher->voucher_id,
                "percent" => $voucher->percent,
                "name" => $voucher->name,
                "expiredDate" => $voucher->expired_date,
                "deleted" => $voucher->deleted,
            ];
        } else {
            $voucher_data = null;
        }

        // Create product array
        $pivot_table = $query->first();

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
                "voucher" => $voucher_data,
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
                    "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s"),
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
    public function destroy(DeleteCustomerRequest $request)
    {
        // $customer = Customer::find($request->user()->id);

        $query = Order::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order = $query->first();

        if ($order->deleted_by !== null) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng đã bị hủy."
            ]);
        }

        // This function cancel by customer so value will be 0
        $order->deleted_by = 0;

        $result = $order->save();

        if (!$result) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Thành công việc hủy Đơn hàng với ID = " . $query->first()->id_delivery
            ]
        );
    }

    public function updateStatus(GetCustomerBasicRequest $request)
    {
        $query = Order::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);

        // Check connection between Customer and Order
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order = $query->first();

        // We only allow customer to change Order Status to Completed state
        if ($order->status === 2) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng này đã được chuyển sang trạng thái hoàn tất."
            ]);
        }

        $products = DB::table("order_product")
            ->where("order_id", "=", $order->id)->get();

        if ($products->count() === 0) {
            return response()->json([
                "success" => false,
                "errors" => "TẠI SAO ĐƠN HÀNG KHÔNG CÓ SẢN PHẨM?? ALO??"
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
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công Đơn hàng sang trạng thái Hoàn tất"
        ]);

        /**
         * Save Order new Status
         * Decrease all Product quantity in from Order
         */
    }
}
