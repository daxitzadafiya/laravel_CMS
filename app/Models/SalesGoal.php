<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesGoal extends Model
{
    protected $fillable = [
        'business_year_start',
        'business_year_end',
        'user_id',
        'status',
    ];

    protected $dates = [
        'business_year_start',
        'business_year_end',
    ];

    public function values()
    {
        return $this->hasMany(SalesGoalValue::class);
    }
}
