<?php

namespace App\Services;

use App\Models\NotificationTag;

class TagService
{
    public function getNotificationTags($request)
    {
        $tags = NotificationTag::query()
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                return $query->orderBy($sort, $request->input('order'));
            });

        return isPaginate($request->input('paginate'))
            ? $tags->paginate($request->input('paginate', 25))
            : $tags->get();
    }
}
