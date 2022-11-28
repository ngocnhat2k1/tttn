<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->level === 0) {
            $display_level = "Admin";
        }
        else {
            $display_level = "Super Admin";
        }

        return [
            "id" => $this->id,
            "userName" => $this->user_name,
            "email" => $this->email,
            "avatar" => $this->avatar,
            "defaultAvatar" => $this->default_avatar,
            "level" => $display_level,
            "createdAt" => date_format($this->created_at, "d/m/Y H:i:s"),
            "updatedAt" => date_format($this->updated_at, "d/m/Y H:i:s")
        ];
    }
}
