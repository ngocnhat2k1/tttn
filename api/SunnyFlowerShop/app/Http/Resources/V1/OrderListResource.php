<?php

namespace App\Http\Resources\V1;

use App\Enums\OrderStatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
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
            "idDelivery" => $this->id_delivery,
            // "description" => $this->description,
            // "voucher_id" => $this->voucher_id,
            "dateOrder" => date("d/m/Y H:i:s", strtotime($this->date_order)),
            "street" => $this->street,
            "ward" => $this->ward,
            "district" => $this->district,
            "province" => $this->province,
            "nameReceiver" => $this->name_receiver,
            "phoneReceiver" => $this->phone_receiver,
            "totalPrice" => $this->total_price,
            "status" => OrderStatusEnum::getStatusAttribute($this->status),
            // "payUrl" => $this->status),
            // "paidType" => $this->paid_type,
        ];
    }
}
