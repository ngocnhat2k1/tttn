<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressDetailResource extends JsonResource
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
            "nameReceiver" => $this->name_receiver,
            "phoneReceiver" => $this->phone_receiver,
            "streetName" => $this->street_name,
            "district" => $this->district,
            "ward" => $this->ward,
            "city" => $this->city
        ];
    }
}
