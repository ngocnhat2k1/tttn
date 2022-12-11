<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Requests\Customer\Store\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Mail\PlaceOrderMail;
use App\Mail\PlaceOrderMailNotifyAdmin;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Momo;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /** GiaoHangNhanh API */
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

    // MOMO PAYMENT
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function checkoutMomo($currentOrder, $id_delivery, $amount, $requestType)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $file = explode("\\", dirname(__FILE__));
        $path_get = $file[0];
        for ($i = 1; $i < sizeof($file) - 2; $i++) {
            $path_get = $path_get . "\\" . $file[$i];
        }

        $path = $path_get . "\\" . "config.json";

        $json = file_get_contents($path);

        // Decode the JSON file
        $json_data = json_decode($json, true);

        $partnerCode = $json_data['partnerCode'];
        $accessKey = $json_data['accessKey'];
        $secretKey = $json_data['secretKey'];
        $orderInfo = "Thanh toán MoMo qua QR";
        // $redirectUrl = route("redirect.page", [
        //     'id' => $currentOrder
        // ]);
        $redirectUrl = "http://127.0.0.1:5500/paySucces.html";
        // $redirectUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
        $ipnUrl = "http://127.0.0.1:5500/index.html";
        $extraData = "";

        if ((int) $requestType === 1) {
            $requestType = "payWithATM";
        }
        // If paid is 2, then it is QR payment
        else if ((int) $requestType === 2) {
            $requestType = "captureWallet";
        }

        // $extraData = $_POST["extraData"];

        $requestId = time() . "";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $id_delivery . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $id_delivery,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json

        //Just a example, please check more in there

        return [
            "success" => true,
            "data" => $data,
            "link" => $jsonResult
        ];

        // return $jsonResult['payUrl'];
    }

    /** PLACE ORDER FUNCTION */
    /** Sending mail */
    public function mail($customer, $order, $listProducts, $title, $text, $subject)
    {
        $userName = $customer->first_name . " " . $customer->last_name;
        $priceOrder = $order->total_price;
        $idDelivery = $order->id_delivery;
        Mail::to($customer->email)->queue(new PlaceOrderMail($subject, $title, $text, $userName, $idDelivery, $priceOrder, $listProducts));
    }

    public function mailNotifyAdmin($admin, $order, $listProducts, $title, $text, $subject)
    {
        $priceOrder = $order->total_price;
        $idDelivery = $order->id_delivery;
        Mail::to($admin->email)->queue(new PlaceOrderMailNotifyAdmin($subject, $title, $text, $idDelivery, $priceOrder, $listProducts));
    }

    public function generateDeliveryCode($orderType)
    {
        $currentTime = time();

        $idDelivery = $currentTime . $orderType;

        return $idDelivery;
    }

    public function redirect(GetCustomerBasicRequest $request)
    {
        // Get Order, Customer and Momo (table) info
        $order = Order::where("id_delivery", '=', $request->orderId)->first();
        $customer = Customer::where("id", "=", $request->user()->id)->first();
        // $customer = Customer::where("id", "=", $order->customer_id)->first();
        $momo = Momo::where("order_id", "=", $order->id)->first();

        // Check if order and momo (order detail) has reached enough condition to continue payment progress
        if ($order->status === -1 || $momo->status === -1) {
            return response()->json([
                "success" => false,
                "errors" => "Đi đâu vậy anh bạn. Đơn hàng đã được hủy rồi!"
            ]);
        }

        // If request message is "Successful." Then proceed to add the rest of products in cart to intermediate table "order_product"
        if ($request->message === "Successful." || $request->message === "Giao dịch thành công.") {
            $arr = [];
            $productsFromTemp = DB::table("order_product_temp")
                ->where("order_id", "=", $order->id)
                ->get();

            for ($i = 0; $i < sizeof($productsFromTemp); $i++) {
                $value = Product::where("id", "=", $productsFromTemp[$i]->product_id)->first();

                $arr[$i]['product_id'] = $value->id;
                $arr[$i]['quantity'] = $productsFromTemp[$i]->quantity;
                $arr[$i]['price'] = $value->price;
                $arr[$i]['percent_sale'] = $value->percent_sale;
            }

            $order->products_temp()->detach();

            $momo->partner_code = $request->partnerCode;
            $momo->order_type = $request->orderType;
            $momo->trans_id = $request->transId;
            $momo->pay_type = $request->payType;
            $momo->status = 1;
            $momo->signature = $request->signature;
            $momo->pay_url = null;
            $momo->save();

            // create order in GiaoHangNhanh immediately
            return $this->completeOrderProcess($arr, $order, $customer);
            // if customer cancels order, change deleted_by value to 0 (order cancelled by customer) and change momo order status to -1
        } else {
            $order->status = -1;
            $order->save();

            $momo->status = -1;
            $momo->partner_code = $request->partnerCode;
            $momo->order_type = $request->orderType;
            $momo->trans_id = $request->transId;
            $momo->signature = $request->signature;
            $momo->pay_url = null;
            $momo->save();

            $order->products_temp()->detach();

            $json_return = [
                "success" => false,
                "errors" => "Giao dịch đã bị hủy bởi người dùng."
            ];

            // Restore voucher usage
            if (empty($order->voucher_id)) {
                return response()->json($json_return);
            }

            $voucher_query = Voucher::where("id", "=", $order->voucher_id);
            if (!$voucher_query->exists()) {
                return response()->json($json_return);
            }

            $voucher = $voucher_query->first();
            $voucher->usage = $voucher->usage + 1;
            $voucher->save();

            return response()->json($json_return);
        }
    }

    public function completeOrderProcess($arr, $order, $customer)
    {
        /** ==> Use Generate Delivery Code HERE */
        // Insert data into intermediate table (order_product)
        for ($i = 0; $i < sizeof($arr); $i++) {
            $productId = Product::find($arr[$i]["product_id"]);
            $order->products()->attach($productId, [
                "quantity" => $arr[$i]["quantity"],
                "price" => $arr[$i]["price"],
                "percent_sale" => $arr[$i]['percent_sale']
            ]);
        }

        // Get list product from current order
        $productsInOrder = [];
        $productsFromOrder = DB::table("order_product")
            ->where("order_id", "=", $order->id)
            ->get();

        if ($order->paid_type === 1 || $order->paid_type === 2) {
            $response = $this->confirmStatus($order);

            if (!empty($response)) { // if response has value that means there has some issue when sending data to GiaoHangNhanh API
                return $response;
            }
        }

        for ($i = 0; $i < sizeof($productsFromOrder); $i++) {
            $product = Product::where("id", "=", $productsFromOrder[$i]->product_id)->first();
            $productsInOrder[$i]['id'] = $product->id;
            $productsInOrder[$i]['name'] = $product->name;
            $productsInOrder[$i]['price'] = $product->price;
            $productsInOrder[$i]['quantity'] = $productsFromOrder[$i]->quantity;
        }

        // Send mail to notify customer has placed order successfully
        $subject = 'Đặt hàng thành công';
        $title = "Quý khách đã đặt hàng thành công";
        $text = "Đơn hàng sẽ được giao đến cho quý khách sớm nhất có thể.";
        $this->mail($customer, $order, $productsInOrder, $title, $text, $subject);

        // Send mail to admin to notify there was new order has been made
        $superAdminEmail = Admin::where("level", "=", 0)->first();
        $subject_notify = "Một đơn hàng mới được tạo.";
        if ($order->paid_type === 0) {
            $title_notify = "Một đơn hàng mới được tạo bởi người dùng.";
        }
        else {
            $title_notify = "Một đơn hàng thanh toán bằng hình momo vừa được tạo.";
        }
        $text_notify = "Vui lòng kiểm tra thông tin thật kĩ càng và nhanh chóng tiến hành gửi cho đơn vị vận chuyển sớm nhất có thể.";
        $this->mailNotifyAdmin($superAdminEmail, $order, $productsInOrder, $title_notify, $text_notify, $subject_notify);

        return response()->json([
            "success" => true,
            "message" => "Đơn hàng đã được đặt hàng thành công."
        ]);
    }

    public function placeOrder($request, $data, $customer)
    {
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
                    "errors" => "Mã giảm giá đã hết hạn, vui lòng sử dụng mã giảm giá khác."
                ]);
            }

            // Check usage
            if ($vouchers->usage === 0) {
                return response()->json([
                    "success" => false,
                    "errors" => "Mã giảm giá đã hết hạn sử dụng, Chúc bạn may mắn lần sau."
                ]);
            }

            // Check Customer has already used vouher (?)
            $voucher_exist_in_customer = Order::where("voucher_id", "=", $vouchers->id)
                ->where("customer_id", "=", $customer->id)->exists();

            if ($voucher_exist_in_customer) {
                return response()->json([
                    "success" => false,
                    "errors" => "Bạn đã sử dụng mã giảm giá này."
                ]);
            }
        } else if (!empty($request->voucher_code)) {
            return response()->json([
                "success" => false,
                "errors" => "Mã giảm giá không tồn tại, Vui lòng kiểm tra lại mã đang được sử dụng."
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
                "errors" => "Một vài sản phẩm không đủ số lượng trong kho. Các sản phẩm đó là: " . implode(", ", $invalid_product_quantity_arr)
            ]);
        }

        // if count !== 0, that mean 1 or 2 product is got deleted or already out of stock
        if ($count !== 0) {
            return response()->json([
                "success" => false,
                "errors" => "Một vài sản phẩm đã bị xóa hoặc bị đã hết hàng."
            ]);
        }
        /** ##### END OF IF CONDITION SECTION ##### */

        $arr = [];
        $total_price = 0;

        for ($i = 0; $i < sizeof($data); $i++) {
            $value = Product::where("id", "=", $data[$i]->product_id)->first();

            $arr[$i]['product_id'] = $value->id;
            $arr[$i]['quantity'] = $data[$i]->quantity;
            $arr[$i]['price'] = $value->price;
            $arr[$i]['percent_sale'] = $value->percent_sale;
            $sale_price = $value->price * $value->percent_sale / 100;

            $total_price = $total_price + (($value->price - $sale_price) * $data[$i]->quantity);
        }

        $voucher_data = $query->first();
        $voucher_sale_value = $voucher_data->percent ?? 0;

        $orderCount = Order::where("customer_id", "=", $customer->id)->get()->count();

        $id_delivery = $this->generateDeliveryCode($request->paidType);

        // Create expected_delivery_time
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        $time = date("H:i:s");

        // Check if current date is follow the rule of calendar
        if ($day === 31 || $day === 30 || $day === 28 || $day === 29) {
            $month = (int) $month + 1;
            $day = "07";
        } else {
            $day = (int) $day + 7;
        }

        $filtered = $request->except("voucherCode", "nameReceiver", "phoneReceiver", "paidType");
        $filtered['date_order'] = date("Y-m-d H:i:s");
        $filtered['voucher_id'] = $voucher_data->id ?? null;
        $filtered["customer_id"] = $customer->id;
        $filtered["expected_delivery_time"] = $year . "-" . $month . "-" . $day . " " . $time;
        $filtered["total_price"] = ceil($total_price - (($total_price * $voucher_sale_value) / 100));

        // Create expired date for voucher which next month after voucher is created
        $filtered['id_delivery'] = $id_delivery;

        if ((int)$filtered['paid_type'] === 1 || (int)$filtered['paid_type'] === 2) {
            $filtered['status'] = 1;
        }

        /** MOMO fucntion */
        $ordesFromCustomer = Order::where('customer_id', "=", $customer->id)->get()->count();
        $currentOrder = $ordesFromCustomer + 1;
        // dd($currentOrder);

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
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        // Detach data from intermediate table (customer_product_cart)
        $customer->customer_product_cart()->detach();

        $order = Order::find($check->id);

        // check order has been created or not
        if (empty($order->id)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành (lần 2) !!"
            ]);
        }

        // If paid type is not 0, then it's online payment
        if ((int) $request->paidType !== 0) {
            $momo = $this->checkoutMomo($currentOrder, $id_delivery, $filtered["total_price"], $request->paidType);

            // Add to momo Table
            $data = [
                "order_id" => $order->id,
                "partner_code" => $momo['data']['partnerCode'],
                "status" => 0,
                "signature" => $momo['data']['signature'],
                "pay_url" => $momo['link']['payUrl']
            ];

            $result = Momo::create($data);

            if (empty($result->id)) {
                return response()->json([
                    "success" => false,
                    "erorrs" => "Đã có lỗi xảy ra trong quá trình vận hành (lần 3) !!"
                ]);
            }

            for ($i = 0; $i < sizeof($arr); $i++) {
                $productId = Product::find($arr[$i]['product_id']);
                $order->products_temp()->attach($productId, [
                    "order_id" => $order->id,
                    "quantity" => $arr[$i]['quantity'],
                ]);
            }

            // return momo link to continue payment process
            // return $momo['data']['partnerCode'];
            return $momo;
        }

        // If paid is 0, then procceed to store order
        return $this->completeOrderProcess($arr, $order, $customer);
    }

    public function store(StoreOrderRequest $request)
    {
        /** ##### IF CONDITION SECTION ##### */
        $customer = Customer::find($request->user()->id); // Later use for detach value from intermediate table

        $data = DB::table("customer_product_cart")
            ->where("customer_id", "=", $customer->id)->get();

        if ($data->count() === 0) {
            return response()->json([
                "success" => false,
                "errors" => "Giỏ hàng của bạn đang trống."
            ]);
        }

        $result = $this->placeOrder($request, $data, $customer);

        return $result;
    }
    /** END OF PLACE ORDER FUNCTION */
}
