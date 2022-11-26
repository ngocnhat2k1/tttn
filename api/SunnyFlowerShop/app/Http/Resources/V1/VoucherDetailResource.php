<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherDetailResource extends JsonResource
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
            "voucherId" => $this->id,
            "name" => $this->name,
            "percent" => $this->percent,
            "usage" => $this->usage,
            "deleted" => $this->deleted,
            "expiredDate" => date("d/m/Y H:i:s", strtotime($this->expired_date)),
            "createdAt" => date_format($this->created_at, "d/m/Y H:i:s"),
            "updatedAt" => date_format($this->updated_at, "d/m/Y H:i:s")
        ];
    }
}
