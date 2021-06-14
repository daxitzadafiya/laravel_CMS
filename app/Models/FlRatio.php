<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlRatio extends Model
{
    protected $fillable = [
        'company_id',
        'business_year_start',
        'business_year_end',
        'f_ratio',
        'l_ratio',
        'user_id',
    ];

    protected $dates = [
        'business_year_start',
        'business_year_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
