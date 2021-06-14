<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountItemCategoryResource;
use App\Http\Resources\HeadCountResource;
use App\Http\Resources\PrefectureResource;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\AccountCategory;
use App\Models\HeadCount;
use App\Models\Prefecture;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;

class MasterController extends Controller
{
    public function index()
    {
        return Cache::rememberForever('all-master', function () {
            return $this->sendResponse([
                'prefectures' => PrefectureResource::collection(Prefecture::all()),
                'subscription_plans' => SubscriptionPlanResource::collection(SubscriptionPlan::all()),
                'head_counts' => HeadCountResource::collection(HeadCount::all()),
                'company_types' => config('reddish.company.types'),
                'company_statuses' => config('reddish.company.statuses'),
                'account_item_categories' => AccountItemCategoryResource::collection(AccountCategory::whereNull('parent_id')->get()),
                'account_item_types' => config('reddish.account_item.types'),
                'account_item_subtypes' => config('reddish.account_item.subtypes'),
            ]);
        });
    }

    public function companyTypes()
    {
        return $this->sendResponse([
            'company_types' => config('reddish.company.types'),
        ]);
    }

    public function companyStatuses()
    {
        return $this->sendResponse([
            'company_statuses' => config('reddish.company.statuses'),
        ]);
    }

    public function prefectures()
    {
        return $this->sendResponse([
            'prefectures' => PrefectureResource::collection(Prefecture::all()),
        ]);
    }

    public function headCounts()
    {
        return $this->sendResponse([
            'head_counts' => HeadCountResource::collection(HeadCount::all()),
        ]);
    }

    public function accountItemCategories($parentCategoryId = null)
    {
        $categories = AccountCategory::where('parent_id', $parentCategoryId)->get();

        return $this->sendResponse([
            'categories' => AccountItemCategoryResource::collection($categories),
        ]);
    }

    public function accountItemTypes()
    {
        return $this->sendResponse([
            'account_item_types' => config('reddish.account_item.types'),
        ]);
    }

    public function accountItemSubTypes()
    {
        return $this->sendResponse([
            'account_item_subtypes' => config('reddish.account_item.subtypes'),
        ]);
    }
}
