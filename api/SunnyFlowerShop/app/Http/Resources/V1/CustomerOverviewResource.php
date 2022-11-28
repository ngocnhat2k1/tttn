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
            "defaultAvatar" => $this->default_avatar,
            "email" => $this->email,
            "subscribed" => $this->subscribed,
            "disabled" => $this->disabled,
            "createdAt" => date_format($this->created_at, "d/m/Y"), 
            "updatedAt" => date_format($this->updated_at, "d/m/Y"),
        ];
    }
}
