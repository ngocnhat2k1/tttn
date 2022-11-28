<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductInCartViewResource extends JsonResource
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
            "productId" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "percentSale" => $this->percent_sale,
            "img" => $this->img,
            "quantity" => $this->pivot->quantity,
            "status" => $this->status,
            "deletedAt" => $this->deleted_at,
            "categories" => CategoryListResource::collection($this->categories)
        ];
    }
}
