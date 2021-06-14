<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationLinkPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_date' => $this->post_date,
            'title' => $this->title,
            'url' => $this->url,
            'publisher' => $this->publisher,
            'status' => (int)$this->status,
            'clicks' => $this->clicks,
            'created_at' => $this->created_at
                ? $this->created_at->format('Y-m-d')
                : null,
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
