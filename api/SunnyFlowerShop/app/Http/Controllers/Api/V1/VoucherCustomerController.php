<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Resources\V1\OrderDetailResource;
use App\Http\Resources\V1\VoucherCustomerOverviewCollection;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Voucher;

class VoucherCustomerController extends Controller
{
    public function index(GetAdminBasicRequest $request, Customer $customer) {
        $vouchers = Order::query()
        ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent", "vouchers.expired_date")
        ->where("customer_id", "=", $customer->id)
        ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");
        
        if (!$vouchers->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Người dùng này chưa dùng mã giảm giá nào."
            ]);
        }

        return new VoucherCustomerOverviewCollection($vouchers->get());
    }

    public function show(GetAdminBasicRequest $request, Customer $customer, Voucher $voucher) {
        $data = Order::query()
        ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent", "vouchers.expired_date")
        ->where("voucher_id", "=", $voucher->id)
        ->where("customer_id", "=", $customer->id)
        ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$data->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Người dùng này chưa dùng mã giảm giá này."
            ]);
        }

        return new OrderDetailResource($data->first());
    }
    
    public function checkVoucher(GetCustomerBasicRequest $request) {
        $data = Voucher::where("name", "=", $request->voucherCode);

        // If voucher is existed, then continue checking voucher attributes
        if ($data->exists()) {

            $vouchers = $data->first();

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
                    "errors" => "Mã giảm giá đã hết hạn sử dụng. Chúc bạn may mắn lần sau."
                ]);
            }

            // Check Customer has already used vouher (?)
            $voucher_exist_in_customer = Order::where("voucher_id", "=", $vouchers->id)
                ->where("customer_id", "=", $request->user()->id)->exists();

            if ($voucher_exist_in_customer) {
                return response()->json([
                    "success" => false,
                    "errors" => "Bạn đã sử dụng mã giảm giá này."
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Xin chúc mừng mã giảm giá tồn tại, bạn có thể sử dụng mã giảm giá này để thực hiện thanh toán.",
                "data" => [
                    "voucherId" => $vouchers->id,
                    "name" => $vouchers->name,
                    "percent" => $vouchers->percent,
                ]
            ]);
        }
        // If voucher doesnt' exists (check voucherCode field)
        else if (!empty($request->voucherCode)) {
            return response()->json([
                "success" => false,
                "errors" => "Mã giảm giá không tồn tại, Vui lòng kiểm tra lại mã đang được sử dụng."
            ]);
        } 
        // If voucherCode field is empty
        else {
            return response()->json([
                "success" => false,
                "errors" => "Vui lòng nhập mã giảm giá trước khi tiến hành kiểm tra."
            ]);
        }
    }
}
