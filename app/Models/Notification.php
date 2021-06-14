<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'category_id', 'type_id', 'is_draft', 'post_date'
    ];


    public function category()
    {
        return $this->belongsTo(NotificationCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(NotificationTag::class, 'notification_notification_tags');
    }

    public function userGroups()
    {
        return $this->belongsToMany(UserGroup::class, 'notifications_user_groups');
    }

    public function scopeOrderByCategory($query, $order = 'asc')
    {
        $query->orderBy(
            NotificationCategory::select('name')
                ->whereColumn('notifications.category_id', 'notification_categories.id'),
            $order
        );
    }
}
