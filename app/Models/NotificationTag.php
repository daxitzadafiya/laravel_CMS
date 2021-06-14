<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTag extends Model
{
    protected $fillable = [
        'name',
    ];

    public function notification()
    {
        return $this->belongsToMany(Notification::class,'notification_notification_tags', 'notification_id', 'notification_tag_id');
    }
}
