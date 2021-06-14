<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FLRatioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'f_ratio' => $this->f_ratio ?? 0,
            'l_ratio' => $this->l_ratio ?? 0,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
