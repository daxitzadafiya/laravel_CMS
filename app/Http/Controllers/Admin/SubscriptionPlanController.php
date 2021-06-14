<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        return $this->sendResponse([
            'subscription_plans' => SubscriptionPlanResource::collection(SubscriptionPlan::all()),
        ]);
    }
}
