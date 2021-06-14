<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllCompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => [
                'id' => $this->type,
                'name' => ucwords($this->type),
            ],
            'display_name' => $this->display_name,
            'postcode' => $this->postcode,
            'prefecture' => new PrefectureResource($this->whenLoaded('prefecture')),
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'head_count' => new HeadCountResource($this->whenLoaded('headCount')),
            'registration_date' => ! empty($this->registration_date)
                ? $this->registration_date->format('Y-m')
                : null,
            'connected_date' => ! empty($this->connected_at)
                ? $this->connected_at->format('Y-m-d')
                : null,
            'status' => config('reddish.company.statuses')[$this->status]
                ?? config('reddish.company.statuses')[0],
        ];
    }
}
