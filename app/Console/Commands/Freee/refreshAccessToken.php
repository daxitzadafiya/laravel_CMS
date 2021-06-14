<?php

namespace App\Console\Commands\Freee;

use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freee:refresh-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get freee api access token using refresh token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $configs = Config::all();

        try {
            $apiUrl = 'https://accounts.secure.freee.co.jp/public_api/token';

            $response = Http::post($apiUrl, [
                'grant_type' => 'refresh_token',
                'client_id' => $configs->where('type', 'freee.api.client-id')
                    ->pluck('value')
                    ->first(),
                'client_secret' => $configs->where('type', 'freee.api.client-secret')
                    ->pluck('value')
                    ->first(),
                'refresh_token' => $configs->where('type', 'freee.api.refresh-token')
                    ->pluck('value')
                    ->first(),
                'redirect_uri' => $configs->where('type', 'freee.api.auth-redirect-uri')
                    ->pluck('value')
                    ->first(),
            ]);

            if (! $response->successful()) {
                Log::channel('freee-api')->info([
                    'message' => 'Unsuccessful Response',
                    'api' => $apiUrl,
                    'response' => $response->json(),
                ]);

                dd($response->json());
            }

            $response = $response->json();

            if (isset($response['access_token']) && isset($response['refresh_token'])) {
                Config::where('type', 'freee.api.access-token')->update([
                    'value' => trim($response['access_token']),
                ]);

                Config::where('type', 'freee.api.refresh-token')->update([
                    'value' => trim($response['refresh_token']),
                ]);
            } else {
                Log::channel('freee-api')->info([
                    'message' => 'access_token or refresh_token missing',
                    'api' => $apiUrl,
                    'response' => $response->json(),
                ]);

                dd($response->json());
            }
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'refresh-access-token exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
