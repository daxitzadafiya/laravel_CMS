<?php

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'posts_count' => Notification::where('category_id', $this->id)->count(),
            'created_at' => $this->created_at
                ? $this->created_at->format('Y-m-d')
                : null,
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
