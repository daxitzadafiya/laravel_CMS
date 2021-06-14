<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    protected $passwordReset = false;

    public function __invoke(ResetPasswordRequest $request)
    {
        Password::broker()->reset(
            $request->only(
                'email', 'password', 'password_confirmation', 'token'
            ), function ($user, $password) {
                $user->password = $password;
                $user->remember_token = Str::random(60);
                $user->save();

                $this->passwordReset = true;

                event(new PasswordReset($user));
        });

        return $this->passwordReset
            ? $this->sendResponse(['message' => __('Password reset successfully.')])
            : $this->sendFail('The given data was invalid.', [
                'token' => __('Incorrect or expired token.'),
            ], 422);
    }
}
