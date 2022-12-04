<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
        // attach category to products (if product doesn't have category)
        $categoryCount = DB::table("category_product")
            ->where("product_id", "=", $this->id);

        if ($categoryCount->get()->count() === 0) {
            $categoriesId = null;
        }
        else {
            $categoriesId = $this->categories[0]->id;
        }

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
            "categoryId" => $categoriesId
        ];
    }
}
