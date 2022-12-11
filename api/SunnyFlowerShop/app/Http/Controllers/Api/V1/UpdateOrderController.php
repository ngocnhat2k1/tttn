<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Admin\Update\UpdateOrderCustomerStatus;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class UpdateOrderController extends Controller
{
    private $shopId = '120932';
    private $token = '7f4667c9-756e-11ed-a83f-5a63c54f968d';
    private $pick_station_id = 71500;
    private $insurance_value = 1000000;
    private $from_name = "Sunny Flower Shop";
    private $from_phone = "0909999999";
    private $from_address = "123 đường Tô Ký";
    private $from_ward_name = "Phường Tân Chánh Hiệp";
    private $from_district_name = "Quận 12";
    private $from_province_name = "Hồ Chí Minh";
    private $cod_amount = 50000;

    public function getProductsFromOrder($order)
    {
        $products = DB::table("order_product")
            ->where("order_id", "=", $order->id)
            ->get();

        $arr = [];

        for ($i = 0; $i < sizeof($products); $i++) {
            $product = Product::find($products[$i]->product_id);
            $init_price = (int)$products[$i]->price * (int)$products[$i]->quantity; // Price before apply sale
            $sale_price = ($init_price * (int)$products[$i]->percent_sale) / 100; // Discount price

            $arr[$i]['name'] = $product->name;
            $arr[$i]['code'] = (string) $product->id;
            $arr[$i]['quantity'] = $products[$i]->quantity;
            $arr[$i]['price'] = ceil($init_price - $sale_price);
            $arr[$i]['length'] = 30;
            $arr[$i]['width'] = 15;
            $arr[$i]['height'] = 45;
            $arr[$i]['category'] = [
                "level1" => $product->categories[0]->name
            ];
        }

        return $arr;
    }

    // Update to confirm state
    public function confirmStatus($order)
    {
        $data = [
            "payment_type_id" => 2,
            "note" => "",
            "from_name" => $this->from_name,
            "from_phone" => $this->from_phone,
            "from_address" => $this->from_address,
            "from_ward_name" => $this->from_ward_name,
            "from_district_name" => $this->from_district_name,
            "from_province_name" => $this->from_province_name,
            "required_note" => "KHONGCHOXEMHANG",
            "return_name" => $this->from_name,
            "return_phone" => $this->from_phone,
            "return_address" => $this->from_address,
            "return_ward_name" => $this->from_ward_name,
            "return_district_name" => $this->from_district_name,
            "return_province_name" => $this->from_province_name,
            "client_order_code" => "",
            "to_name" => $order->name_receiver,
            "to_phone" => $order->phone_receiver,
            "to_address" => $order->street,
            "to_ward_name" => $order->ward,
            "to_district_name" => $order->district,
            "to_province_name" => $order->province, // "TP Hồ Chí Minh"
            "cod_amount" => $this->cod_amount,
            "content" => "Sunny Flower Shop gửi hàng",
            "weight" => 500,
            "length" => 35,
            "width" => 20,
            "height" => 50,
            "pick_station_id" => $this->pick_station_id,
            "deliver_station_id" => null,
            "insurance_value" => $this->insurance_value,
            "service_id" => 53320,
            "service_type_id" => 2,
            "coupon" => null,
            "pick_shift" => null,
            "pickup_time" => 1665272576,
            "items" => $this->getProductsFromOrder($order)
        ];

        // Make a request to create order in Giao Hang Nhanh site
        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create', $data);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        /** Change some value in order table */
        $order->expected_delivery_time = date("Y-m-d H:i:s", strtotime($response['data']['expected_delivery_time']));
        $order->order_code = $response['data']['order_code'];
        $order->total_fee = $response['data']['total_fee'];
        $order->trans_type = $response['data']['trans_type'];
        $order->status = 2;
        $order->save();
    }

    public function cancelStatus($order)
    {
        if (empty($order->order_code)) {
            $order->status = -1;
            $order->save();
            return;
        }

        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/switch-status/cancel', [
                "order_codes" => [$order->order_code]
            ]);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        $order->status = -1;
        $order->save();
    }

    public function updateStatus(UpdateOrderCustomerStatus $request, Order $order)
    {
        /** Update current status of order
         * Status can change from "Pending" to "Confirmed" and vice versa if admin dectects any supscious actions
         * Status can only be changed from "Confirmed" to "Completed", no reverse allow
         * When status is in "Completed" status, quantity was store in pivot table "order_prodcut" with use to minus the quantity of products in "products" table
         */

        // Check order (Soft) Delete state and Order status
        if ($order->status ===  -1 || $order->status === 6) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể thay đổi trạng thái Đơn hàng nếu Đơn hàng đang ở trạng thái hủy hoặc hoàn tất."
            ]);
        }

        $state = (int) $request->state;

        if ((int) $order->status === $state) {
            return response()->json([
                "success" => false,
                "errors" => 'Đơn hàng hiện đang ở trạng thái: ' . OrderStatusEnum::getStatusAttribute($state) . ''
            ]);
        }

        switch ($state) {
                // start with 2 because "1" is for momo state
            case 2: // Confirm state
                $response = $this->confirmStatus($order);
                break;

            case -1: // Cancel state
                $response = $this->cancelStatus($order);
                break;

            default:
                if ($state === 1) {
                    return response()->json([
                        "success" => false,
                        "errors" => "Trạng thái state = " . $state . " chỉ dành cho thanh toán momo và không thể tùy ý tự chỉnh."
                    ]);
                }

                return response()->json([
                    "success" => false,
                    "errors" => "Trạng thái state = " . $state . " không tồn tại. Vui lòng chọn trạng thái có số <= 6"
                ]);
                break;
        }

        if (!empty($response)) { // if response has value that means there has some issue when sending data to GiaoHangNhanh API
            return $response;
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công trạng thái của Đơn hàng có ID = " . $order->id .  " sang trạng thái " . OrderStatusEnum::getStatusAttribute($state)
        ]);
    }

    public function refreshState(GetAdminBasicRequest $request)
    {
        $orders = Order::all();

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

        return response()->json([
            "message" => "Trạng thái của đơn hàng đã được cập nhật."
        ]);
    }

    // Get District ID
    public function getDistrictID($provinceId, $districtName)
    {
        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                "province_id" => $provinceId
            ]);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        $district_data = $response['data'];
        for ($i = 0; $i < sizeof($district_data); $i++) {
            if ($districtName === $district_data[$i]['DistrictName']) {
                return $district_data[$i]['DistrictID'];
            }
        }
    }

    // Get Ward Code
    public function getWardCode($districtId, $wardName)
    {
        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id', [
                "district_id" => $districtId
            ]);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        $ward_data = $response['data'];
        for ($i = 0; $i < sizeof($ward_data); $i++) {
            if ($wardName === $ward_data[$i]['WardName']) {
                return $ward_data[$i]['WardCode'];
            }
        }
    }

    // Get Ward Code
    public function getProvinceId($provinceName)
    {
        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province');

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        $district_data = $response['data'];
        for ($i = 0; $i < sizeof($district_data); $i++) {
            if ($provinceName === $district_data[$i]['ProvinceName']) {
                return $district_data[$i]['ProvinceID'];
            }
        }
    }

    // TAKE NOTE THAT THIS FUNCTION IS BROKEN, still figure out why it doesn't work
    // Use for preview order before creating Order in GiaoHangNhanh site
    public function preview(GetAdminBasicRequest $request, Order $order)
    {
        // Shop info
        $provinceId_main = $this->getProvinceId($this->from_province_name);
        $districtId_main = $this->getDistrictID($provinceId_main, $this->from_district_name);
        $wardCode_main = $this->getWardCode($districtId_main, $this->from_ward_name);

        // Order info
        $provinceId = $this->getProvinceId($order->province);
        $districtId = $this->getDistrictID($provinceId, $order->district);
        $wardCode = $this->getWardCode($districtId, $order->ward);
        $address = $order->street . ", " . $order->ward . ", " . $order->district . ", " . $order->province . ", Vietnam";

        $data = [
            "payment_type_id" => 2,
            "note" => "",
            "required_note" => "KHONGCHOXEMHANG",
            "return_phone" => $this->from_phone,
            "return_address" => $this->from_address,
            "return_district_id" => $districtId_main,
            "return_ward_code" => $wardCode_main,
            "client_order_code" => "",
            "to_name" => $order->name_receiver,
            "to_phone" => $order->phone_receiver,
            "to_address" => $address, //"72 Thành Thái, Phường 14, Quận 10, Hồ Chí Minh, Vietnam"
            "to_ward_code" => $wardCode,
            "to_district_id" => $districtId,
            "cod_amount" => $this->cod_amount,
            "content" => "Preview - Sunny Flower Shop gửi hàng.",
            "weight" => 500,
            "length" => 35,
            "width" => 20,
            "height" => 50,
            "pick_station_id" => $this->pick_station_id,
            "insurance_value" => $this->insurance_value,
            "service_id" => 53320,
            "service_type_id" => 2,
            "coupon" => null,
            "pick_shift" => null,
            "items" => $this->getProductsFromOrder($order)
        ];

        $response = Http::withHeaders([
            'ShopId' => $this->shopId,
            'Token' => $this->token
        ])
            ->accept('application/json')
            ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail', $data);

        // Handle errors
        if ($response->failed()) {
            return json_decode($response);
        }

        return json_decode($response);
    }
}
