<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscriptionPlans = [
            [
                'id' => 1,
                'name' => 'Free',
                'is_default' => 'Y',
            ],
        ];

        foreach ($subscriptionPlans as $subscriptionPlan) {
            DB::table('subscription_plans')->insert([
                'id' => $subscriptionPlan['id'],
                'name' => $subscriptionPlan['name'],
                'is_default' => $subscriptionPlan['is_default'],
            ]);
        }
    }
}
