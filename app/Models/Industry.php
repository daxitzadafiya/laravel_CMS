<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(Industry::class, 'parent_id', 'id');
    }
}
