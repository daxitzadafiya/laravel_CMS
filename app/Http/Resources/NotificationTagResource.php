<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTagResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'posts_count' => '2',
            'created_at' => $this->created_at
                ? $this->created_at->format('Y-m-d')
                : null,
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
