<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'issue_date',
        'type',
        'account_item_id',
        'amount',
        'vat',
        'description',
        'entry_side',
    ];

    public $incrementing = false;
    public $timestamps = false;

    public function scopeCredit($query)
    {
        return $query->where('entry_side', 'credit');
    }

    public function scopeDebit($query)
    {
        return $query->where('entry_side', 'debit');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function accountItem()
    {
        return $this->belongsTo(AccountItem::class);
    }
}
