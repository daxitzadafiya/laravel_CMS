<?php

namespace App\Http\Resources;

use App\Models\NotificationRead;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => new NotificationCategoryResource($this->whenLoaded('category')),
            'type' => collect(config('reddish.notification.types'))->where('id', $this->type_id)->first(),
            'tags' => $this->tags,
            'group' => $this->userGroups,
            'user_notification_read_count' => NotificationRead::where('notification_id',$this->id)->count(),
            'post_date' => $this->post_date,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
