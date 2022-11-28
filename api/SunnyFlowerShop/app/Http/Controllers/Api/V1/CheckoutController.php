<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Http\Requests\Customer\Store\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Mail\PlaceOrderMail;
use App\Models\Customer;
use App\Models\Momo;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
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
    public function mail($customer, $order, $listProducts)
    {
        $title = "Quý khách đã đặt hàng thành công";
        $text = "Đơn hàng sẽ được giao đến cho quý khách sớm nhất có thể.";
        $userName = $customer->first_name . " " . $customer->last_name;
        $priceOrder = $order->total_price;
        $idDelivery = $order->id_delivery;
        Mail::to($customer->email)->send(new PlaceOrderMail($title, $text, $userName, $idDelivery, $priceOrder, $listProducts));
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
        if ($order->deleted_by !== null || $momo->status === -1) {
            return response()->json([
                "success" => false,
                "errors" => "Oops! Something went wrong. This order has already been cancelled"
            ]);
        }

        // If request message is "Successful." Then proceed to add the rest of products in cart to intermediate table "order_product"
        if ($request->message === "Successful." || $request->message === "Giao dịch thành công.") {
            $arr = [];
            $productsFromCart = DB::table("customer_product_cart")
                ->where("customer_id", "=", $order->customer_id)
                ->get();

            for ($i = 0; $i < sizeof($productsFromCart); $i++) {
                $value = Product::where("id", "=", $productsFromCart[$i]->product_id)->first();

                $arr[$i]['product_id'] = $value->id;
                $arr[$i]['quantity'] = $productsFromCart[$i]->quantity;
                $arr[$i]['price'] = $value->price;
                $arr[$i]['percent_sale'] = $value->percent_sale;
            }

            $momo->partner_code = $request->partnerCode;
            $momo->order_type = $request->orderType;
            $momo->trans_id = $request->transId;
            $momo->pay_type = $request->payType;
            $momo->status = 1;
            $momo->signature = $request->signature;
            $momo->save();

            return $this->completeOrderProcess($arr, $order, $customer);
            // if customer cancels order, change deleted_by value to 0 (order cancelled by customer) and change momo order status to -1
        } else {
            $order->deleted_by = 0;
            $order->save();

            $momo->status = -1;
            $momo->save();

            $json_return = [
                "success" => false,
                "errors" => "Transaction has been cancelled by customer"
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
            $confirm = $order->products()->attach($productId, [
                "quantity" => $arr[$i]["quantity"],
                "price" => $arr[$i]["price"],
                "percent_sale" => $arr[$i]['percent_sale']
            ]);
        }

        // Detach data from intermediate table (customer_product_cart)
        $detach = $customer->customer_product_cart()->detach();

        // Get list product from current order
        $productsInOrder = [];
        $productsFromOrder = DB::table("order_product")
            ->where("order_id", "=", $order->id)
            ->get();

        for ($i = 0; $i < sizeof($productsFromOrder); $i++) {
            $product = Product::where("id", "=", $productsFromOrder[$i]->product_id)->first();
            $productsInOrder[$i]['id'] = $product->id;
            $productsInOrder[$i]['name'] = $product->name;
            $productsInOrder[$i]['price'] = $product->price;
            $productsInOrder[$i]['quantity'] = $productsFromOrder[$i]->quantity;
        }

        // Send mail to notify customer has placed order successfully
        $this->mail($customer, $order, $productsInOrder);

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
                ->where("customer_id", "=", $customer->id)->exists();

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

        $orderCount = Order::where("customer_id", "=", $customer->id)->get()->count();

        $id_delivery = $this->generateDeliveryCode($request->paidType);

        $filtered = $request->except("voucherCode", "dateOrder", "nameReceiver", "phoneReceiver", "paidType");
        $filtered['voucher_id'] = $voucher_data->id ?? null;
        $filtered["customer_id"] = $customer->id;
        $filtered["total_price"] = $total_price - (($total_price * $voucher_sale_value) / 100);

        $filtered['id_delivery'] = $id_delivery;

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
                "errors" => "Something went wrong"
            ]);
        }

        $order = Order::find($check->id);

        // check order has been created or not
        if (empty($order->id)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
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
                "signature" => $momo['data']['signature']
            ];

            $result = Momo::create($data);

            if (empty($result->id)) {
                return response()->json([
                    "success" => false,
                    "erorrs" => "An unexpected error has occurred"
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
                "errors" => "Your cart is empty or Your Order is currently in progress"
            ]);
        }

        $result = $this->placeOrder($request, $data, $customer);

        return $result;
    }
    /** END OF PLACE ORDER FUNCTION */
}
