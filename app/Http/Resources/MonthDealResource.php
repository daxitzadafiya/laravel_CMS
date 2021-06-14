<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthDealResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'month' => [
                'id' => $this->month,
                'name' => Carbon::createFromFormat('Y-m-d', $this->month . '-01')->format('M'),
            ],
            'amount' => (int) $this->amount,
        ];
    }
}
