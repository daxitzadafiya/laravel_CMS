<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'parent_id',
        'name',
        'long_name',
        'shortcut1',
        'shortcut2',
    ];

    public $incrementing = false;
    public $timestamps = false;
}
