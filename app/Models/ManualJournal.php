<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualJournal extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'issue_date',
        'account_item_id',
        'amount',
        'vat',
        'description',
        'entry_side',
    ];

    public $incrementing = false;
    public $timestamps = false;
}
