<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function store(AuthRequest $request)
    {
        if (! auth()->attempt($request->validated())) {
            return $this->sendFail('The given data was invalid.', [
                'email' => __('Incorrect login credentials.'),
            ], 422);
        }

        $user = auth()->user();

        if ($user->role != 'SA' && $user->role != 'A') {
            return $this->sendFail('The given data was invalid.', [
                'email' => __('Incorrect login credentials.'),
            ], 422);
        }

        $accessToken = auth()->user()->createToken('adminAuthToken')->plainTextToken;

        $data = [
            'message' => __('Authenticated successfully.'),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ];

        return $this->sendResponse($data);
    }

    public function show()
    {
        $data = [
            'user' => new UserResource(auth()->user()),
        ];

        return $this->sendResponse($data);
    }

    public function destroy()
    {
        request()->user()->currentAccessToken()->delete();

        $data = [
            'message' => __('Logged out successfully.'),
        ];

        return $this->sendResponse($data);
    }
}
