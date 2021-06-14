<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTag;

class NotificationService
{
    public function getNotifications($request)
    {
        $notification = Notification::query()
            ->with('category', 'tags', 'userGroups')
            ->where('is_draft', $request->input('is_draft', 0))
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');

                switch ($sort) {
                    case 'category': return $query->OrderByCategory($order);
                    default: return $query->orderBy($sort, $order);
                }

            })
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('post_date', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->input('category_id'), function ($query, $category_id) {
                return $query->where(function ($query) use ($category_id) {
                    $query->where('category_id', '=', $category_id);
                });
            })
            ->when($request->input('tag_id'), function ($query, $tag_id) {
                return $query->where(function ($query) use ($tag_id) {
                    $query->WhereHas('tags', function ($query) use ($tag_id) {
                            $query->where('notification_tag_id', '=', $tag_id);
                        });
                });
            });

        return isPaginate($request->input('paginate'))
            ? $notification->paginate($request->input('paginate', 25))
            : $notification->get();
    }

    public function getRelatedNotifications($notification)
    {
        $notifications = Notification::query()
            ->with('category')
            ->where('is_draft', 0)
            ->where('id', '!=', $notification->id)
            ->where('category_id', $notification->category_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()->toArray();

        $latest = array();
        if (count($notifications) < 3) {
            $limit = 3 - count($notifications);
            $latest = Notification::query()
                ->with('category')
                ->where('is_draft', 0)
                ->where('id', '!=', $notification->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()->toArray();
        }

        return array_merge($notifications, $latest);
    }

    public function getTagIds($tags)
    {
        $temp = [];
        foreach ($tags as $value) {
            $tag = NotificationTag::where('name', $value)->first();
            if ($tag) {
                $temp[] = $tag->id;
            } else {
                $notificationTag = new NotificationTag;
                $notificationTag->name = $value;
                $notificationTag->save();
                $temp[] = $notificationTag->id;
            }
        }
        return $temp;
    }
}
