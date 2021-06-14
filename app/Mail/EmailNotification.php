<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $notification;
    protected $user;

    public function __construct($notification, $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject(__('【CPアプリ】問合せ: ' . $this->notification['type_id']))
            ->from($this->user->email, $this->user->last_name . ' ' . $this->user->first_name)
            ->cc(config('mail.system_bcc_email'))
            ->view('emails.notifications', [
                'user' => $this->user,
                'content' => (object) $this->notification,
            ]);
    }
}
