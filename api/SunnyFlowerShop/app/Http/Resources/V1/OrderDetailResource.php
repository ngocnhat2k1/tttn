<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            "id" => $this->id,
            "customerId" => $this->customer_id,
            // "voucherId" => $this->voucher_id,
            "voucher" => [
                "nameVoucher" => $this->name,
                "percentSale" => $this->percent,
                "expiredDate" => $this->expired_date,
                "deleted" => $this->deleted,
            ],
            "dataOrder" => $this->date_order,
            "address" => $this->address,
            "nameReceiver" => $this->name_receiver,
            "phoneReceiver" => $this->phone_receiver,
            "totalPrice" => $this->total_price,
            "status" => $this->status,
            "paidType" => $this->paid_type,
            "deletedBy" => $this->deleted_by,
            "products" => ProductDetailResource::collection($this->products)
        ];
    }
}
