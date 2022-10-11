<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedBackDetailResource extends JsonResource
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
            "id" => $this->pivot->id,
            "customerId" => $this->pivot->customer_id,
            "productId" => $this->pivot->product_id,
            "quality" => $this->pivot->quality,
            "comment" => $this->pivot->comment
        ];
    }
}
