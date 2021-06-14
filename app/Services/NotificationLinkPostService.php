<?php

namespace App\Services;

use App\Models\NotificationLinkPost;

class NotificationLinkPostService
{
    public function getNotificationLinkPost($request)
    {
        $notification_link_post = NotificationLinkPost::query()
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');
                return $query->orderBy($sort, $order);
            })
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('post_date', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('publisher', 'like', "%{$search}%")
                        ->orWhere('clicks', 'like', "%{$search}%");
                });
            });

        return isPaginate($request->input('paginate'))
            ? $notification_link_post->paginate($request->input('paginate', 25))
            : $notification_link_post->get();
    }
}
