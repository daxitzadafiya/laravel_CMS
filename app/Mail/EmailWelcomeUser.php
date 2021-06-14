<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailWelcomeUser extends Mailable
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject(__('Welcome to CP'))
            ->view('emails.welcomeUser')
            ->with([
                'user' => $this->user,
            ]);
    }
}
