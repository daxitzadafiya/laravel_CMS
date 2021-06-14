<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeadCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $headCounts = [
            [
                'id' => 1,
                'name' => '2~5人',
            ],
            [
                'id' => 2,
                'name' => '6~10人',
            ],
            [
                'id' => 3,
                'name' => '11~20人',
            ],
            [
                'id' => 13,
                'name' => '21~50人',
            ],
            [
                'id' => 14,
                'name' => '51~100人',
            ],
            [
                'id' => 15,
                'name' => '101~300人',
            ],
            [
                'id' => 18,
                'name' => '301~500人',
            ],
            [
                'id' => 16,
                'name' => '501~1000人',
            ],
            [
                'id' => 17,
                'name' => '1001人以上',
            ],
        ];

        foreach ($headCounts as $headCount) {
            DB::table('head_counts')->insert([
                'id' => $headCount['id'],
                'name' => $headCount['name'],
            ]);
        }
    }
}
