<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderDetailResource;
use App\Http\Resources\V1\VoucherCustomerOverviewCollection;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherCustomerController extends Controller
{
    public function index(Customer $customer) {
        $vouchers = Order::query()
        ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent", "vouchers.expired_date")
        ->where("customer_id", "=", $customer->id)
        ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");
        
        if (!$vouchers->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "This user hasn't used any voucher yet"
            ]);
        }

        return new VoucherCustomerOverviewCollection($vouchers->get());
    }

    public function show(Customer $customer, Voucher $voucher) {
        $data = Order::query()
        ->addSelect("orders.*", "vouchers.id as voucher_id", "vouchers.name", "vouchers.percent", "vouchers.expired_date")
        ->where("voucher_id", "=", $voucher->id)
        ->where("customer_id", "=", $customer->id)
        ->join("vouchers", "orders.voucher_id", "=", "vouchers.id");

        if (!$data->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "This customer hasn't used this voucher yet"
            ]);
        }

        return new OrderDetailResource($data->first());
    }
}
