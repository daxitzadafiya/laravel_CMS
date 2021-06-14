<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTags extends Model
{
    protected $table = 'notification_notification_tags';

    protected $fillable = [
        'notification_id', 'notification_tag_id'
    ];

    public $timestamps = false;
}
