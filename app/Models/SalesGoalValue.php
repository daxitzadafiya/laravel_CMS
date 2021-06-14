<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesGoalValue extends Model
{
    protected $fillable = [
        'year',
        'month',
        'goal',
    ];
}
