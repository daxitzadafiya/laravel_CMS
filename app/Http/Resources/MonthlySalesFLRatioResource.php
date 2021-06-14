<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlySalesFLRatioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'month' => [
                'id' => $this->year . '-' . $this->month,
                'name' => $this->month . '月',
                'long_name' => $this->year . '年' . $this->month . '月',
            ],
            'fl_cost' => [
                'total' => [
                    'amount' => (int) $this->sale,
                    'percentage' => $this->sale
                        ? round(($this->f_cost + $this->l_cost) / $this->sale * 100, 1)
                        : 0,
                ],
                'f_cost' => [
                    'amount' => (int) $this->f_cost,
                    'percentage' => $this->sale
                        ? round($this->f_cost / $this->sale * 100, 1)
                        : 0,
                ],
                'l_cost' => [
                    'amount' => (int) $this->l_cost,
                    'percentage' => $this->sale
                        ? round($this->l_cost / $this->sale * 100, 1)
                        : 0,
                ],
            ],
            'other_cost' => [
                'amount' => (int) $this->sale - $this->f_cost - $this->l_cost,
                'percentage' => $this->sale
                    ? round(($this->sale - $this->f_cost - $this->l_cost) / $this->sale * 100, 1)
                    : 0,
            ],
        ];
    }
}
