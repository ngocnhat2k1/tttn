<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "percentSale" => $this->percent_sale,
            "img" => $this->img,
            "quantity" => $this->quantity,
            "quality" => $this->quality,
            "status" => $this->status,
            "deletedAt" => $this->deleted_at,
            "categoryId" =>$this->categories[0]->id
        ];
    }
}
