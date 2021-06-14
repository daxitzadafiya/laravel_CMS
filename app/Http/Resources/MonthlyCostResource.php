<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyCostResource extends JsonResource
{
    public function toArray($request)
    {
        $totalCost = $this->f_cost + $this->l_cost + $this->o_cost;

        return [
            'month' => [
                'id' => $this->year . '-' . $this->month,
                'name' => $this->month . '月',
                'long_name' => $this->year . '年' . $this->month . '月',
            ],
            'fl_cost' => [
                'total' => [
                    'amount' => (int) $this->f_cost + $this->l_cost,
                    'percentage' => $totalCost
                        ? round(($this->f_cost + $this->l_cost) / $totalCost * 100, 1)
                        : 0,
                ],
                'f_cost' => [
                    'amount' => (int) $this->f_cost,
                    'percentage' => $totalCost
                        ? round($this->f_cost / $totalCost * 100, 1)
                        : 0,
                ],
                'l_cost' => [
                    'amount' => (int) $this->l_cost,
                    'percentage' => $totalCost
                        ? round($this->l_cost / $totalCost * 100, 1)
                        : 0,
                ],
            ],
            'other_cost' => [
                'amount' => (int) $this->o_cost,
                'percentage' => $totalCost
                    ? round($this->o_cost / $totalCost * 100, 1)
                    : 0,
            ],
        ];
    }
}
