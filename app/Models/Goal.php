<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'year',
        'month',
        'goal',
    ];
}
