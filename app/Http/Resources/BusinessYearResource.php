<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessYearResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'term' => $this['term'],
            'start_date' => $this['start_date']->format('Y-m-d'),
            'end_date' => $this['end_date']->format('Y-m-d'),
        ];
    }
}
