<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConfigSeeder::class,
            PrefectureSeeder::class,
            IndustrySeeder::class,
            HeadCountSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
