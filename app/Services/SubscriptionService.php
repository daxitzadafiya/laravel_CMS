<?php

namespace App\Services;

use App\Models\CompanySubscription;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function changePlan($company, $planId): void
    {
        DB::transaction(function () use($company, $planId) {
            $company->subscriptions()
                ->where('status', 'active')
                ->update([
                    'status' => 'changed',
                ]);

            CompanySubscription::create([
                'company_id' => $company->id,
                'subscription_plan_id' => $planId,
                'status' => 'active',
            ]);
        });
    }
}
