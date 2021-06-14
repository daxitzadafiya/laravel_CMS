<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'id' => 1,
                'name' => '集客・マーケティング支援について',
            ],
            [
                'id' => 2,
                'name' => '融資・補助金・資金調達について',
            ],
            [
                'id' => 3,
                'name' => 'アプリでの目標値設定について',
            ],
            [
                'id' => 4,
                'name' => 'アプリ内の表示データについて',
            ],
            [
                'id' => 5,
                'name' => '知人紹介プログラムについて',
            ],
            [
                'id' => 6,
                'name' => 'その他',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('faq_categories')->insert([
                'id' => $category['id'],
                'name' => $category['name'],
                'created_at' => NOW(),
                'updated_at' => NOW(),
            ]);
        }
    }
}
