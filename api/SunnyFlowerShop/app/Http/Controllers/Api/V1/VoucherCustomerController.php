<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
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
}
