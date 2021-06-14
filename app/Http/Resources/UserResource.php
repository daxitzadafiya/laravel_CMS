<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'first_name_kana' => $this->when($this->first_name_kana, $this->first_name_kana),
            'last_name_kana' => $this->when($this->last_name_kana, $this->last_name_kana),
            'email' => $this->email,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'position' => $this->when($this->position, $this->position),
            'role' => $this->role,
            'groups' => $this->whenLoaded('userGroups'),
            'photo' => ! empty($this->photo)
                ? url('storage/user/photos/' . $this->photo)
                : url('avatar.svg'),
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
