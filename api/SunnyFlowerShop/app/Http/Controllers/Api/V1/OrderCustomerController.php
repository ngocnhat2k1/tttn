<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentDisplayEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Admin\Update\UpdateOrderCustomerRequest;
use App\Http\Resources\V1\OrderListCollection;
use App\Mail\OrderDeliveredNotify;
use App\Mail\OrderDeliveredState;
use App\Models\Customer;
use App\Models\Momo;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrderCustomerController extends Controller
{
    private $shopId = '120932';
    private $token = '7f4667c9-756e-11ed-a83f-5a63c54f968d';

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
            case 'picked ':
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

            default:
                return;
        }

        return $state;
    }

    /**  Use for refresh order status */
    public function refreshState($order)
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

    /** Use for refresh all orders status */
    public function refreshAllStateOrder($orders)
    {
        for ($i = 0; $i < sizeof($orders); $i++) {
            if (empty($orders[$i]->order_code) || $orders[$i]->status === 6) continue;

            $response = Http::withHeaders([
                'ShopId' => $this->shopId,
                'Token' => $this->token
            ])
                ->accept('application/json')
                ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail', [
                    "order_code" => $orders[$i]->order_code
                ]);

            // Handle errors
            if ($response->failed()) {
                return json_decode($response);
            }

            // $arr[$index] = $response['data'];
            // $index++;
            $orders[$i]->status = (int) OrderStatusEnum::getStatusAttributeReverse($response['data']['status']);
            $orders[$i]->save();
        }
    }

    /** Main Functions */
    public function index(GetAdminBasicRequest $request, Customer $customer)
    {
        $order = $customer->orders;

        if (empty($order)) {
            return response()->json([
                "success" => false,
                "errors" => "Ngươi dùng chưa đặt đơn hàng nào."
            ]);
        }

        $this->refreshAllStateOrder($order);

        $arr = [];

        for ($i = 0; $i < sizeof($order); $i++) {
            $arr[$i]['id'] = $order[$i]->id;
            $arr[$i]['customerId'] = $order[$i]->customer_id;
            $arr[$i]['idDelivery'] = $order[$i]->id_delivery;
            $arr[$i]['dateOrder'] = $order[$i]->date_order;
            $arr[$i]['address'] = $order[$i]->street . ", " . $order[$i]->ward . ", " . $order[$i]->district . ", " . $order[$i]->province . ", Việt Nam";
            $arr[$i]['nameReceiver'] = $order[$i]->name_receiver;
            $arr[$i]['totalPrice'] = $order[$i]->total_price;
            $arr[$i]['phoneReceiver'] = $order[$i]->phone_receiver;
            $arr[$i]['paidType'] = PaymentDisplayEnum::getPaymentDisplayAttribute($order[$i]->paid_type);
            $arr[$i]['status'] = OrderStatusEnum::getStatusAttribute($order[$i]->status);
        }

        return $this->paginator($arr, $request, 12);

        // return response()->json([
        //     "success" => true,
        //     "data" => new OrderListCollection($order)
        // ]);
    }

    public function show(GetAdminBasicRequest $request, Customer $customer, Order $order)
    {
        // Check existence of Customer and Order via Customer ID and Order ID
        $query = Order::where("orders.id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Sản phẩm và ID Đơn hàng."
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
        $pivot_table = Order::find($order->id);

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

        if ($data->status < 6) {
            $this->refreshState($data);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "customer" => [
                    "customerId" => $customer->id,
                    "firstName" => $customer->first_name,
                    "lastName" => $customer->last_name,
                    "email" => $customer->email,
                    "avatar" => $customer->avatar,
                    "defaultAvatar" => $customer->default_avatar,
                ],
                "voucher" => $voucher_data,
                "order" => [
                    "id" => $data->id,
                    "idDelivery" => $data->id_delivery,
                    "dateOrder" => $data->date_order,
                    "address" => $data->street . ", " . $data->ward . ", " . $data->district . ", " . $data->province . ", Việt Nam",
                    "nameReceiver" => $data->name_receiver,
                    "phoneReceiver" => $data->phone_receiver,
                    "totalPrice" => $data->total_price,
                    "status" => OrderStatusEnum::getStatusAttribute($data->status),
                    "paidType" => PaymentDisplayEnum::getPaymentDisplayAttribute($data->paid_type),
                    "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                    "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s"),
                ],
                "products" => $productsInOrder
            ]
        ]);
    }

    public function update(UpdateOrderCustomerRequest $request, Customer $customer, Order $order)
    {
        $query = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order_data = $query->first();

        // Checking Order_status or Deleted_by column isNull
        // 0 fresh new and null is order doesn't get cancelled
        if ($order_data->status === 0) {
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
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Cập nhật thành công thông tin Đơn hàng với ID = " . $order->id
            ]);
        }

        return response()->json([
            "success" => false,
            "errors" => "Vui lòng kiểm tra lại tình trạng của đơn hàng."
        ]);
    }

    public function updateStatus(Request $request, Customer $customer, Order $order)
    {
        return ($request->user());
        /** Update current status of order
         * Status can change from "Pending" to "Confirmed" and vice versa if admin dectects any supscious actions
         * Status can only be changed from "Confirmed" to "Completed", no reverse allow
         * When status is in "Completed" status, quantity was store in pivot table "order_prodcut" with use to minus the quantity of products in "products" table
         */

        // Check order (Soft) Delete state and Order status
        if ($order->status === -1 || $order->status === 6) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể thay đổi trạng thái Đơn hàng nếu Đơn hàng đang ở trạng thái hủy hoặc hoàn tất."
            ]);
        }

        $query = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        // Check Connection between Customer and Order
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order_data = $query->first();

        $state = (int) $request->state;
        $order_data->status = $state;
        $result = $order_data->save();

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Thay đổi thành công trạng thái của Đơn hàng với ID = " . $order->id .  " sang trạng thái " . OrderStatusEnum::getStatusAttribute($state)
        ]);
    }

    public function notifyOrder(GetAdminBasicRequest $request, Customer $customer, Order $order)
    {
        // Check order (Soft) Delete state and Order status
        if ($order->status === -1 || $order->status === 6) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng đang ở trạng thái hủy hoặc hoàn tất."
            ]);
        }

        $query = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id);

        // Check Connection between Customer and Order
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đơn hàng không tồn tại."
            ]);
        }

        $order_data = $query->first();

        // Send
        $userName = $customer->first_name . " " . $customer->last_name;
        $priceOrder = $order->total_price;
        $idDelivery = $order->id_delivery;

        // If state is 1, then Send Notify to customer that Order has been delivered
        if ((int) $request->state === 1) {
            $title = "Đơn hàng đã được giao thành công";
            Mail::to($customer->email)->queue(new OrderDeliveredState($title, $userName, $idDelivery, $priceOrder));
        }
        // If state is 0, then send Notify to Customer to Click "Completed" button to completed order state.
        else {
            $title = "Vui lòng xác nhận đơn hàng đã được giao";
            Mail::to($customer->email)->queue(new OrderDeliveredNotify($title, $userName, $idDelivery, $priceOrder));
        }

        return response()->json([
            "success" => true,
            "message" => "Gửi thành công thông báo cho khách hàng."
        ]);
    }

    public function destroy(Customer $customer, Order $order, DeleteAdminBasicRequest $request)
    {
        // Checking The connection between Order and Customer
        $order_data = Order::where("id", "=", $order->id)
            ->where("customer_id", "=", $customer->id)
            ->exists();

        if (!$order_data) {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng kiểm tra lại ID Khách hàng và ID Đơn hàng."
            ]);
        }

        // If Order state is 2 (Completed state), then return
        if ($order->status === 2) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể hủy Đơn hàng khi Đơn hàng đang ở trạng thái Hoàn tất."
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
                    'message' => "Hủy thành công Đơn hàng với ID = " . $order->id . " cho Khách hàng có ID = " . $customer->id
                ]
            );

            // If value state is not 1, it will be Reverse Delete
        } else {
            // Checking The connection between Order and Customer
            if (!$order_data) {
                return response()->json([
                    "success" => false,
                    "errors" => "Vui lòng kiểm tra lại ID Đơn hàng và ID Khách hàng."
                ]);
            }

            // Checking whether Deleted_by column is null or not
            if ($order->status !== -1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đơn hàng với iD = " . $order->id . " đã được hoàn tác việc hủy đơn."
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
                    'message' => "Hoàn tác thành công việc hủy đơn hành = " . $order->id . " for Customer ID = " . $customer->id
                ]
            );
        }
    }
}
