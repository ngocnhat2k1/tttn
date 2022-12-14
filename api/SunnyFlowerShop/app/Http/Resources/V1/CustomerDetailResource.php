<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
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
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "email" => $this->email,
            "avatar" => $this->avatar,
            "defaultAvatar" => $this->default_avatar,
            "subscribed" => $this->subscribed,
            "disabled" => $this->disabled,
            "createdAt" => date_format($this->created_at, "d/m/Y"),
            "updatedAt" => date_format($this->updated_at, "d/m/Y"),
        ];
    }
}
