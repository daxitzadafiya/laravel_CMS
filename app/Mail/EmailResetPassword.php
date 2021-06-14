<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailResetPassword extends Mailable
{
    protected $resetUrl;

    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject(__('Reset Password'))
            ->view('emails.resetPassword')
            ->with([
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
