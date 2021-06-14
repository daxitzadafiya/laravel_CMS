<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'category_id');
    }
}
