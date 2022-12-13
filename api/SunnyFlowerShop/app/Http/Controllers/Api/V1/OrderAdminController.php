<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentDisplayEnum;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderAdminController extends Controller
{
    private $shopId = '120932';
    private $token = '7f4667c9-756e-11ed-a83f-5a63c54f968d';

    /** ADMIN FUNCTIONs */
    public function paginator($arr, $request)
    {
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

    /** Main Functions */
    public function index(GetAdminBasicRequest $request)
    {
        $check = Order::get()->count();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Danh sách đơn hàng hiện đang trống."
            ]);
        }

        // $orders = Order::with("customers")->paginate(10);
        $orders = Order::orderBy("created_at", "DESC")->get();
        $arr = [];
        $index = 0;

        // Do two for loop to add all order to array
        for ($i = 0; $i < sizeof($orders); $i++) { // First loop is used to get into Customer index at $i
            // Get customer info
            $customer = Customer::find($orders[$i]->customer_id);

            // Customer field
            $arr[$index]['customerId'] = $customer->id;
            $arr[$index]['orderId'] = $orders[$i]->id;
            $arr[$index]['firstName'] = $customer->first_name;
            $arr[$index]['lastName'] = $customer->last_name;

            // Order field
            if ($orders[$i]->voucher_id !== null) {
                $voucher_code = Voucher::find($orders[$i]->voucher_id)->name;
            }
            else {
                $voucher_code = null;
            }

            $arr[$index]['voucherCode'] = $voucher_code;
            $arr[$index]['idDelivery'] = $orders[$i]->id_delivery;
            $arr[$index]['dateOrder'] = $orders[$i]->date_order;
            $arr[$index]['expectedDeliveryTime'] = date("Y-m-d", strtotime($orders[$i]->expected_delivery_time));
            $arr[$index]['orderCode'] = $orders[$i]->order_code;
            $arr[$index]['address'] = $orders[$i]->street . ", " . $orders[$i]->ward . ", " . $orders[$i]->district . ", " . $orders[$i]->province . ", Việt Nam";
            $arr[$index]['nameReceiver'] = $orders[$i]->name_receiver;
            $arr[$index]['phoneReceiver'] = $orders[$i]->phone_receiver;
            $arr[$index]['price'] = $orders[$i]->total_price;

            // Confirm Order status to display it correctly
            if ($orders[$i]->status < 6) {
                $this->refreshStateOrder($orders[$i]);
            }

            $arr[$index]['status'] = OrderStatusEnum::getStatusAttribute($orders[$i]->status);
            $arr[$index]['paidType'] = PaymentDisplayEnum::getPaymentDisplayAttribute($orders[$i]->paid_type);
            $arr[$index]['createdAt'] = date_format($orders[$i]->created_at, "d/m/Y H:i:s");
            $arr[$index]['updatedAt'] = date_format($orders[$i]->updated_at, "d/m/Y H:i:s");

            $index++; // index for array we currently use
        }

        $new_arr = $this->paginator($arr, $request);

        return $new_arr;

        // return new OrderCustomerListCollection($customers_orders);
    }

    public function show(GetAdminBasicRequest $request, Order $order)
    {
        // Check existence of Customer and Order via Customer ID and Order ID
        $voucher_query = Voucher::where("id", "=", $order->voucher_id);
        $customer_query = Customer::where("id", "=", $order->customer_id);

        if (!$voucher_query->exists() || !$customer_query->exists()) {
            if ($order->voucher_id !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đơn hàng có vài thông tin không hợp lệ, vui lòng kiểm tra lại trước khi cho phép hiển thị trên màn hình."
                ]);
            }
        }

        // Confirm Order status to display it correctly
        if ($order->status < 6) {
            $this->refreshStateOrder($order);
        }

        if ($order->voucher_id !== null) {
            $voucher = $voucher_query->first();
            $order['voucher'] = new VoucherDetailResource($voucher);
        } else {
            $order['voucher'] = null;
        }
        $order['customer'] = $customer_query->first();

        $products = $order->products;
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
                ->where("order_id", "=", $order->id)
                ->first();

            $productsInOrder[$i]['quantity'] = $productQuantity->quantity;
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order" => [
                    "customer" => new CustomerOverviewResource($order->customer),
                    "voucher" => $order->voucher,
                    "orderId" => $order->id,
                    "idDelivery" => $order->id_delivery,
                    "orderCode" => $order->order_code,
                    "dateOrder" => $order->date_order,
                    "expectedDeliveryTime" => date("Y-m-d", strtotime($order->expected_delivery_time)),
                    "address" => $order->street . ", " . $order->ward . ", " . $order->district . ", " . $order->province . ", Việt Nam",
                    "nameReceiver" => $order->name_receiver,
                    "phoneReceiver" => $order->phone_receiver,
                    "totalPrice" => $order->total_price,
                    "status" => OrderStatusEnum::getStatusAttribute($order->status),
                    "paidType" => PaymentDisplayEnum::getPaymentDisplayAttribute($order->paid_type),
                    "products" => $productsInOrder
                ]
            ]
        ]);

        // "products" => ProductDetailResource::collection($this->products)

        // return response()->json([
        //     "success" => true,
        // "data" => new OrderDetailResource($order)
        // ]);
    }

    public function showViaIdDelivery(GetAdminBasicRequest $request)
    {
        // Check existence of Customer and Order via Customer ID and Order ID
        $order_query = Order::where("id_delivery", "=", $request->id);
        // $customer_query = Customer::where("id", "=", $order->customer_id);

        if (!$order_query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order = $order_query->first();

        // Confirm Order status to display it correctly
        if ($order->status < 6) {
            $this->refreshStateOrder($order);
        }

        $customer_query = Customer::where("id", "=", $order->customer_id)->first();

        if ($order->voucher_id !== null) {
            $voucher = Voucher::where("id", "=", $order->voucher_id)->first();
            $order['voucher'] = new VoucherDetailResource($voucher);
        } else {
            $order['voucher'] = null;
        }
        $order['customer'] = $customer_query->first();

        $products = $order->products;
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
                ->where("order_id", "=", $order->id)
                ->first();

            $productsInOrder[$i]['quantity'] = $productQuantity->quantity;
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order" => [
                    "customer" => new CustomerOverviewResource($order->customer),
                    "voucher" => $order->voucher,
                    "orderId" => $order->id,
                    "idDelivery" => $order->id_delivery,
                    "orderCode" => $order->order_code,
                    "dateOrder" => $order->date_order,
                    "expectedDeliveryTime" => date("Y-m-d", strtotime($order->expected_delivery_time)),
                    "address" => $order->street . ", " . $order->ward . ", " . $order->district . ", " . $order->province . ", Việt Nam",
                    "nameReceiver" => $order->name_receiver,
                    "phoneReceiver" => $order->phone_receiver,
                    "totalPrice" => $order->total_price,
                    "status" => OrderStatusEnum::getStatusAttribute($order->status),
                    "paidType" => PaymentDisplayEnum::getPaymentDisplayAttribute($order->paid_type),
                    "products" => $productsInOrder
                ]
            ]
        ]);
    }

    public function destroy(Order $order, DeleteAdminBasicRequest $request)
    {
        // If Order state is 2 (Completed state), then return
        if ($order->status === 6) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể hủy đơn hàng nếu Đơn hàng đang trong trạng thái Hoàn tất."
            ]);
        }

        // Check state to switch between (Soft) Delete and Reverse Delete
        // If value state is 1, it will be (Soft) Delete
        // State right here is delete state not Order state
        if ((int)$request->state !== 0) {
            // Checking whether Deleted_by column is null or not
            if ($order->status === -1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đơn hàng với ID = " . $order->id . " đã được hủy."
                ]);
            }

            // If not then asign value to order->deleted_by by 1 (1 for admin; 0 for customer)
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
                    'message' => "Hủy thành công Đơn hàng có ID = " . $order->id
                ]
            );

            // If value state is not 1, it will be Reverse Delete
        } else {

            // Checking whether Deleted_by column is null or not
            if ($order->status !== -1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đơn hàng với ID = " . $order->id . " đã được hoàn tác việc hủy đơn."
                ]);
            }

            // If not then asign value to order->deleted_by by 1 (1 for admin; 0 for customer)
            $order->status = 0;
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
                    'message' => "Hoàn tác thành công việc hủy Đơn hàng với ID = " . $order->id
                ]
            );
        }
    }
}
