<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailContactUs extends Mailable
{
    protected $data;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject(__('【CPアプリ】問合せ: ' . $this->data['type']))
            ->from($this->user->email, $this->user->last_name . ' ' . $this->user->first_name . ' / ' . $this->user->company->display_name)
            ->cc(config('mail.system_bcc_email'))
            ->view('emails.contactUs', [
                'user' => $this->user,
                'content' => (object) $this->data,
            ]);
    }
}
