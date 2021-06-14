<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Send success response.
     */
    public function sendResponse($data = [], $metaData = [], $status = 200)
    {
        $response = [
            'data' => $data,
        ];

        foreach ($metaData  as $key => $value) {
            $response[$key] = $value;
        }

        return response()->json($response, $status);
    }

    /**
     * Send fail response.
     */
    public function sendFail($message = '', $errors = [], $status = 422)
    {
        $response = [
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json(['error' => $response], $status);
    }

    /**
     * Send error response.
     */
    public function sendError($message, $status)
    {
        return response()->json($message, $status);
    }
}
