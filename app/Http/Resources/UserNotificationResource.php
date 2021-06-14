<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
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
            'post_date' => $this->post_date,
            'is_read' => in_array($this->id, $request->readNotification) ? 1 : 0,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
