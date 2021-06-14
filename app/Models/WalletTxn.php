<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTxn extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'date',
        'walletable_id',
        'entry_side',
        'amount',
        'balance',
        'due_amount',
        'description',
        'status',
    ];

    protected $dates = [
        'date',
    ];

    public $incrementing = false;
    public $timestamps = false;

    public function walletable()
    {
        return $this->belongsTo(Walletable::class);
    }
}
