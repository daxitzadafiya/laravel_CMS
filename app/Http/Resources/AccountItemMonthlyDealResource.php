<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountItemMonthlyDealResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account_item' => [
                'id' => $this->id,
                'name' => $this->name,
                'type' => $this->subtype == 'F' || $this->subtype == 'L' ? $this->subtype : '',
            ],
            'deals' => MonthDealResource::collection($this->monthly_deals),
        ];
    }
}
