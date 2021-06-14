<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __invoke(ChangePasswordRequest $request)
    {
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return $this->sendFail('The given data was invalid.', [
                'email' => __('Current password is wrong.'),
            ], 422);
        }

        auth()->user()->password = $request->input('new_password');
        auth()->user()->save();

        return $this->sendResponse([
            'message' => __('Password updated successfully.'),
        ]);
    }
}
