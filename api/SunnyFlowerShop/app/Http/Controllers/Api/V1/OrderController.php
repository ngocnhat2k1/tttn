<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentDisplayEnum;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Delete\DeleteCustomerRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Resources\V1\OrderListCollection;
use App\Http\Resources\V1\ProductDetailResource;
use App\Models\Momo;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $shopId = '120932';
    private $token = '7f4667c9-756e-11ed-a83f-5a63c54f968d';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Paginator
    public function paginator($arr, $request, $page)
    {
        $total = count($arr);
        $per_page = $page;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    // Checking order status in GiaoHangNhanh site
    public function getOrderStatus($state)
    {
        switch ($state) {
                // Picking state
            case 'ready_to_pick':
            case 'picking':
                $state = "ready_to_pick";
                break;

                // Picked State
            case 'picked':
                $state = "picked";
                break;

                // Delivering State
            case 'delivering':
            case 'transporting':
            case 'money_collect_picking':
            case 'storing':
            case 'sorting':
            case 'money_collect_delivering':
                $state = "delivering";
                break;

                // Cancel State
            case 'cancel':
                $state = "cancel";
                break;

                // Return/ Damage/ Lost/ Processing State
            case 'return':
            case 'return_transporting':
            case 'return_sorting':
            case 'returning':
            case 'return_fail':
            case 'returned':
            case 'exception':
            case 'damage':
            case 'lost':
            case 'delivered':
            case 'delivery_fail':
            case 'waiting_to_return':
                $state = "processing";
                break;

            default:
                return;
        }

        return $state;
    }

    /**  Use for refresh order status */
    public function refreshStateOrder($order)
    {
        if (empty($order->order_code)) return;

        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail', [
                "order_code" => $order->order_code
            ]);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        // $arr[$index] = $response['data'];
        // $index++;

        $state = $this->getOrderStatus($response['data']['status']);

        $order->status = (int) OrderStatusEnum::getStatusAttributeReverse($state);
        $order->save();
    }

    public function index(GetCustomerBasicRequest $request)
    {
        $data = Order::where("customer_id", "=", $request->user()->id)->orderBy("created_at", "DESC")->get();
        $count = $data->count();

        if (empty($count)) {
            return response()->json([
                'success' => false,
                "errors" => "Danh sách đơn hàng hiện đang trống."
            ]);
        }

        $arr = [];

        for ($i = 0; $i < sizeof($data); $i++) {
            $arr[$i]['id'] = $data[$i]->id;
            $arr[$i]['customerId'] = $data[$i]->customer_id;

            if ($data[$i]->voucher_id !== null) {
                $voucher_code = Voucher::find($data[$i]->voucher_id)->name;
            }
            else {
                $voucher_code = null;
            }

            $arr[$i]['voucherCode'] = $voucher_code;
            $arr[$i]['idDelivery'] = $data[$i]->id_delivery;
            $arr[$i]['orderCode'] = $data[$i]->order_code;
            $arr[$i]['expectedDeliveryTime'] = date("Y-m-d", strtotime($data[$i]->expected_delivery_time));
            $arr[$i]['dateOrder'] = $data[$i]->date_order;
            $arr[$i]['address'] = $data[$i]->street . ", " . $data[$i]->ward . ", " . $data[$i]->district . ", " . $data[$i]->province . ", Việt Nam";
            $arr[$i]['nameReceiver'] = $data[$i]->name_receiver;
            $arr[$i]['totalPrice'] = $data[$i]->total_price;
            $arr[$i]['phoneReceiver'] = $data[$i]->phone_receiver;

            if ($data[$i]->status < 6) {
                $this->refreshStateOrder($data[$i]);
            }

            $arr[$i]['status'] = OrderStatusEnum::getStatusAttribute($data[$i]->status);
            $arr[$i]['paidType'] = PaymentDisplayEnum::getPaymentDisplayAttribute($data[$i]->paid_type);

            $momo = Momo::where("order_id", "=", $data[$i]->id)
                ->where("status", "<>", -1);

            if ($momo->exists()) {
                $arr[$i]['payUrl'] = $momo->first()->pay_url;
            } else {
                $arr[$i]['payUrl'] = null;
            }
        }

        return $this->paginator($arr, $request, 12);

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

        if ($data->status < 6) {
            $this->refreshStateOrder($data);
        }

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

        $products = $pivot_table->products;
        $productsInOrder = [];

        for ($i = 0; $i < sizeof($products); $i++) {
            $productsInOrder[$i]['id'] = $products[$i]->id;
            $productsInOrder[$i]['name'] = $products[$i]->name;
            $productsInOrder[$i]['description'] = $products[$i]->description;
            $productsInOrder[$i]['price'] = $products[$i]->price;
            $productsInOrder[$i]['percentSale'] = $products[$i]->percent_sale;
            $productsInOrder[$i]['img'] = $products[$i]->img;

            $productQuantity = DB::table("order_product")
                ->where("product_id", "=", $products[$i]->id)
                ->where("order_id", "=", $data->id)
                ->first();

            $productsInOrder[$i]['quantity'] = $productQuantity->quantity;
        }

        $momo = Momo::where("order_id", "=", $data->id)
            ->where("status", "<>", -1);

        if ($momo->exists()) {
            $data['payUrl'] = $momo->first()->pay_url;
        } else {
            $data['payUrl'] = null;
        }

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
                    "orderCode" => $data->order_code,
                    "dateOrder" => $data->date_order,
                    "expectedDeliveryTime" => date("Y-m-d", strtotime($data->expected_delivery_time)),
                    "address" => $data->street . ", " . $data->ward . ", " . $data->district . ", " . $data->province . ", Việt Nam",
                    "nameReceiver" => $data->name_receiver,
                    "phoneReceiver" => $data->phone_receiver,
                    "totalPrice" => $data->total_price,
                    "status" => OrderStatusEnum::getStatusAttribute($data->status),
                    "paidType" => PaymentDisplayEnum::getPaymentDisplayAttribute($data->paid_type),
                    "payUrl" => $data->payUrl,
                    "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s"),
                ],
                "products" => $productsInOrder
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

        if ($data->status < 6) {
            $this->refreshStateOrder($data);
        }

        if ($data->voucher_id !== null) {
            $voucher = Voucher::where("id", "=", $data->voucher_id)->first();

            $voucher_data = [
                "voucherId" => $voucher->id,
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

        $products = $pivot_table->products;
        $productsInOrder = [];

        for ($i = 0; $i < sizeof($products); $i++) {
            $productsInOrder[$i]['id'] = $products[$i]->id;
            $productsInOrder[$i]['name'] = $products[$i]->name;
            $productsInOrder[$i]['description'] = $products[$i]->description;
            $productsInOrder[$i]['price'] = $products[$i]->price;
            $productsInOrder[$i]['percentSale'] = $products[$i]->percent_sale;
            $productsInOrder[$i]['img'] = $products[$i]->img;

            $productQuantity = DB::table("order_product")
                ->where("product_id", "=", $products[$i]->id)
                ->where("order_id", "=", $data->id)
                ->first();

            $productsInOrder[$i]['quantity'] = $productQuantity->quantity;
        }

        $momo = Momo::where("order_id", "=", $data->id)
            ->where("status", "<>", -1);

        if ($momo->exists()) {
            $data['payUrl'] = $momo->first()->pay_url;
        } else {
            $data['payUrl'] = null;
        }

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
                    "orderCode" => $data->order_code,
                    "dateOrder" => $data->date_order,
                    "expectedDeliveryTime" => date("Y-m-d", strtotime($data->expected_delivery_time)),
                    "address" => $data->street . ", " . $data->ward . ", " . $data->district . ", " . $data->province . ", Việt Nam",
                    "nameReceiver" => $data->name_receiver,
                    "phoneReceiver" => $data->phone_receiver,
                    "totalPrice" => $data->total_price,
                    "status" => OrderStatusEnum::getStatusAttribute($data->status),
                    "paidType" => PaymentDisplayEnum::getPaymentDisplayAttribute($data->paid_type),
                    "payUrl" => $data->payUrl,
                    "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s"),
                ],
                "products" => $productsInOrder
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

        $query = Order::where("id_delivery", "=", $request->idDelivery)
            ->where("customer_id", "=", $request->user()->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order = $query->first();

        if ($order->status === -1) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng đã bị hủy."
            ]);
        }

        // This function cancel by customer so value will be 0
        $order->status = -1;

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
                'message' => "Thành công việc hủy Đơn hàng với ID = " . $request->idDelivery
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
        if ($order->status == 6) {
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

        $order->status = 6;
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
