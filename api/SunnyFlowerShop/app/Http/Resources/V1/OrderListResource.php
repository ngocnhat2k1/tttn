<?php

namespace App\Http\Resources\V1;

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
            // "description" => $this->description,
            // "voucher_id" => $this->voucher_id,
            "dateOrder" => $this->date_order,
            "address" => $this->address,
            "nameReceiver" => $this->name_receiver,
            "phoneReceiver" => $this->phone_receiver,
            "totalPrice" => $this->total_price,
            "status" => $this->status,
            // "paidType" => $this->paid_type,
            "deletedBy" => $this->deleted_by,
        ];
    }
}
