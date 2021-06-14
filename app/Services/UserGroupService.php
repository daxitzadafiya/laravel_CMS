<?php

namespace App\Services;

use App\Models\UserGroup;

class userGroupService
{
    public function getGroups($request)
    {
        $groups = UserGroup::query()
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');
                return $query->orderBy($sort, $order);
            });

        return isPaginate($request->input('paginate'))
            ? $groups->paginate($request->input('paginate', 25))
            : $groups->get();
    }
}
