<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            [
                'type' => 'freee.api.client-id',
                'value' => '28ca5288eac4bf6f2fb630c34397d73d30882e52d984ec6a6cb5600d09ba0d55',
                'created_at' => NOW(),
            ], [
                'type' => 'freee.api.client-secret',
                'value' => 'c82bfe29f8daeddbbe253faf4c9d344fbba4aab0a53c59ababa89fc9bfaa15d4',
                'created_at' => NOW(),
            ], [
                'type' => 'freee.api.access-token',
                'value' => null,
                'created_at' => NOW(),
            ], [
                'type' => 'freee.api.refresh-token',
                'value' => null,
                'created_at' => NOW(),
            ], [
                'type' => 'freee.api.auth-redirect-uri',
                'value' => 'urn:ietf:wg:oauth:2.0:oob',
                'created_at' => NOW(),
            ],
        ]);
    }
}
