<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOverviewResource extends JsonResource
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
            // "currentPage" => $this->current_page,
            "id" => $this->id,
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "avatar" => $this->avatar,
            "email" => $this->email,
            "subscribed" => $this->subscribed
        ];
    }
}
