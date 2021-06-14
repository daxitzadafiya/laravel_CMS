<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationLinkPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_date', 'title', 'url', 'publisher', 'status', 'clicks'
    ];
}
