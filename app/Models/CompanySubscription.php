<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySubscription extends Model
{
    protected $fillable = [
        'company_id',
        'subscription_plan_id',
        'status',
    ];
}
