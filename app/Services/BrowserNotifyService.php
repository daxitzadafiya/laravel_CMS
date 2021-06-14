<?php

namespace App\Services;

class BrowserNotifyService
{

    public $pusherKey;
    public $pusherToken;

    public function __construct() {
        $this->pusherKey = env('PUSHER_KEY', config('reddish.browser_pusher.pusher_key'));
        $this->pusherToken = env('PUSHER_AUTH_TOKEN', config('reddish.browser_pusher.pusher_auth_token'));
    }

    public function sendNotifyToAll($data)
    {
        $end_point = 'https://api.webpushr.com/v1/notification/send/attribute';
        $http_header = array(
            "Content-Type: Application/Json",
            "webpushrKey: " . $this->pusherKey,
            "webpushrAuthToken: " . $this->pusherToken
        );
        $req_data = array(
            'title'         => $data['title'], // required
            'message'         => $data['message'], // required
            'target_url'    => $data['url'], // required
            'name'            => isset($data['name']) ? $data['name'] : '',
            'icon'            => isset($data['icon']) ? $data['icon'] : url("assets/images/logo-small-190x190px.png"),
            'auto_hide'        => 1
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

    public function sendNotifyToSingleSubscriber($data) {
        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';
        $http_header = array(
            "Content-Type: Application/Json",
            "webpushrKey: " . $this->pusherKey,
            "webpushrAuthToken: " . $this->pusherToken
        );
        $req_data = array(
            'title'         => $data['title'], // required
            'message'         => $data['message'], // required
            'target_url'    => $data['url'], // required
            'sid'           => $data['sid'], // required
            'name'            => isset($data['name']) ? $data['name'] : '',
            'icon'            => isset($data['icon']) ? $data['icon'] : url("assets/images/logo-small-190x190px.png"),
            'auto_hide'        => 1,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }
}
