<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Walletable extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'type',
        'walletable_balance',
        'last_balance',
    ];

    public $incrementing = false;
}
