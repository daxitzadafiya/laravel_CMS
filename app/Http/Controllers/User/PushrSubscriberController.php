<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class PushrSubscriberController extends Controller
{
    public function __invoke($user_id, $sid) {
        $data['success'] = false;

        $user = User::find($user_id);
        if ($user) {
            // $subscriberKeys = $user->subscriber_id == '' ? [] : explode(',', $user->subscriber_id);
            // $subscriberKeys[] = $sid;
            // $subscriberKeys = array_unique($subscriberKeys); // Should have no duplicate keys to avoid multiple operations
            // $newSubscriberKeys = implode(',', $subscriberKeys);
            $data['is_updated'] = $user->update(['subscriber_id'=> $sid]);
            $data['success'] = true;
        }

        return $this->sendResponse($data);
    }
}
