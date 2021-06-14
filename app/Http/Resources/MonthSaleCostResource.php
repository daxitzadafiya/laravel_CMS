<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MonthSaleCostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'month' => [
                'id' => $this->year_month,
                'name' => $this->month . '月',
            ],
            'sale' => (int) $this->income,
            'cost' => (int) $this->expense,
        ];
    }
}
