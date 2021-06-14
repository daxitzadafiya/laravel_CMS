<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => new FaqCategoryResource($this->whenLoaded('category')),
            'order' => $this->order,
            'question' => $this->question,
            'answer' => $this->answer,
            'status' => $this->status,
        ];
    }
}
