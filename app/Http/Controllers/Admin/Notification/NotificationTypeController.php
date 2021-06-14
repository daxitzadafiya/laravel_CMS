<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;

class NotificationTypeController extends Controller
{
    public function index()
    {
        return $this->sendResponse(['types' => config('reddish.notification.types')]);
    }
}
