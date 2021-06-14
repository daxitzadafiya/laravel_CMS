<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletableResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => (int) $this->last_balance,
            'last_updated_date' => ! empty($this->updated_at)
                ? $this->updated_at->format('Y-m-d')
                : null,
        ];
    }
}
