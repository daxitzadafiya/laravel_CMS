<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthGoalResource extends JsonResource
{
    public function toArray($request)
    {
        $response = [
            'month' => [
                'id' => $this->year_month ?? $this->year . '-' . $this->month,
                'name' => $this->month . '月',
            ],
            'goal' => (int) $this->goal ?? 0,
        ];

        if (isset($this->sale) && isset($this->goal)) {
            $response['sale'] = (int) $this->sale ?? 0;
            $response['percentage'] = $this->goal
                ? round($this->sale / $this->goal * 100, 1)
                : 0;
        }

        return $response;
    }
}
