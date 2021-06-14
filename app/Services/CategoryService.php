<?php

namespace App\Services;

use App\Models\NotificationCategory;

class CategoryService
{
    public function getNotificationCategories($request)
    {
        $categories = NotificationCategory::query()
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');
                return $query->orderBy($sort, $order);
            });

        return isPaginate($request->input('paginate'))
            ? $categories->paginate($request->input('paginate', 25))
            : $categories->get();
    }
}
