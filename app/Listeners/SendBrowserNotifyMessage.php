<?php

namespace App\Listeners;

use App\Events\SendBrowserNotify;
use App\Mail\EmailNotification;
use App\Models\User;
use App\Services\BrowserNotifyService;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBrowserNotifyMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendBrowserNotify  $event
     * @return void
     */
    public function handle(SendBrowserNotify $event)
    {
        try {
            if(isset($event) && !empty($event)) {
                foreach($event as $value){
                    $data[] = $value;
                }
                $data = isset($data[0]) ? $data[0] : [];

                $users = User::with('preferences')->where('role', 'U')->get();
                $notification = $data['notification'];

                $browserNotify = new BrowserNotifyService();
                if ($users->count()) {
                    foreach ($users as $user) {
                        $sendPushNotification = $user->subscriber_id == '' ? 'send_email' : 'notify'; // Send email or notification

                        if ($user->preferences()->where(['name'=> 'notification.browser', 'value'=> 0])->first()) {
                            // IOS device Or blocked notifications - So email will be sent
                            $sendPushNotification = 'send_email';
                        } else if ($notification->type_id == 'promotion' || $notification->type_id == 'general') {
                            // If allowed Notification
                            $isSetNotification = $user->preferences()->where(['name'=> 'notification.' . $notification->type_id, 'value'=> 0])->first();
                            if ($isSetNotification) {
                                $sendPushNotification = 'off';
                            }
                        }

                        if ($sendPushNotification == 'notify') {
                            $data['sid'] = $user->subscriber_id;
                            $browserNotify->sendNotifyToSingleSubscriber($data);

                        } else if ($sendPushNotification == 'send_email') {
                            // email events
                            Mail::to($user->email)
                            ->send(new EmailNotification($notification, $user));
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
