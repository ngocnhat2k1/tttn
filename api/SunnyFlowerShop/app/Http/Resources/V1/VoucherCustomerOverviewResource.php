<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherCustomerOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "orderId" => $this->id,
            "voucherId" => $this->voucher_id,
            "nameVoucher" => $this->name,
            "percent" => $this->percent,
            "usedDate" => $this->date_order,
            "expiredDate" => $this->expired_date,
        ];
    }
}
