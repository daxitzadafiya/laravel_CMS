<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'amount' => [
                'incoming' => $this->entry_side == 'income'
                    ? (int) $this->amount
                    : $this->description,
                'outgoing' => $this->entry_side == 'expense'
                    ? (int) $this->amount
                    : $this->description,
            ],
            'balance' => (int) $this->balance,
        ];
    }
}
