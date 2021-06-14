<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;

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

        if ($user->role != 'U') {
            return $this->sendFail('The given data was invalid.', [
                'email' => __('Incorrect login credentials.'),
            ], 422);
        }

        $company = Company::find($user->company_id);

        if ($company->status != 1) {
            return $this->sendFail('Company not connected.', [
                'company' => __('Company not connected.'),
            ], 422);
        }

        $accessToken = auth()->user()->createToken('adminAuthToken')->plainTextToken;

        $company->current_month_logins = $company->current_month_logins + 1;
        $company->last_login_at = NOW();
        $company->save();

        $data = [
            'message' => __('Authenticated successfully.'),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user->load('company')),
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
