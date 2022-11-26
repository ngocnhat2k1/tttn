<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerRegisterResource extends JsonResource
{
    // REMINDER: This resource will be deleted after project reach final stage in progression

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
            "password" => $this->password,
            "subscribed" => $this->subscribed,            
            "updatedAt" => date_format($this->updated_at,"d/m/Y"),
            "createdAt" => date_format($this->created_at,"d/m/Y"),
        ];
    }
}
