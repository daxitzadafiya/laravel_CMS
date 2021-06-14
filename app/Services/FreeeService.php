<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FreeeService
{
    public function apiResponse($endPoint, $dataKey)
    {
        $apiUrl = config('reddish.freee_api_base_url') . $endPoint;
        $response = Http::withToken(getFreeeAccessToken())->get($apiUrl);

        if (! $response->successful()) {
            Log::channel('freee-api')->info([
                'message' => 'Unsuccessful Response',
                'api' => $apiUrl,
                'response' => $response->json(),
            ]);

            dd($response->json());
        }

        $response = $response->json();

        if (! isset($response[$dataKey]) || ! is_array($response[$dataKey])) {
            Log::channel('freee-api')->info([
                'message' => $dataKey . ' array not found',
                'api' => $apiUrl,
                'response' => $response,
            ]);

            dd($response);
        }

        return $response;
    }
}
