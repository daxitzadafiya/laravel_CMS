<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Mail\EmailResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    protected $resetPageUrl = '';
    protected $resetLinkSent = false;

    public function __invoke(ForgotPasswordRequest $request)
    {
        $this->resetPageUrl = $request->input('reset_url');

        Password::broker()->sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                Mail::to($user->email)
                    ->send(new EmailResetPassword($this->resetPageUrl . $token));

                $this->resetLinkSent = true;
            }
        );

        return $this->resetLinkSent
            ? $this->sendResponse(['message' => __('Reset link sent to your email.')])
            : $this->sendError(__('Unable to send reset link.'), 500);
    }
}
