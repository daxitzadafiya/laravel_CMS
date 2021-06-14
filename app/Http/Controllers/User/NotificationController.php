<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationLinkPostResource;
use App\Http\Resources\UserNotificationResource;
use App\Http\Resources\PaginationResource;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\NotificationLinkPost;
use App\Services\NotificationLinkPostService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationLinkPostService;

    public function __construct(
        NotificationLinkPostService $notificationLinkPostService,
        NotificationService $notificationService
    ) {
        $this->notificationLinkPostService = $notificationLinkPostService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $notification = $this->notificationService->getNotifications($request);
        $readNotification = NotificationRead::where('user_id', auth()->user()->id)->pluck('notification_id')->toArray();

        $request->merge([
            'readNotification' => $readNotification,
        ]);

        return $this->sendResponse(
            ['notification' => UserNotificationResource::collection($notification)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($notification)]
                : []
        );
    }

    public function show(Notification $notification, Request $request)
    {
        $notification->load('category');
        $relatedPosts = $this->notificationService->getRelatedNotifications($notification);
        $readNotification = NotificationRead::where('user_id', auth()->user()->id)->pluck('notification_id')->toArray();

        $request->merge([
            'readNotification' => $readNotification,
        ]);

        return $this->sendResponse([
            'notification' => new UserNotificationResource($notification),
            'related_posts' => $relatedPosts,
        ]);
    }

    public function getNotificationLinkPost(Request $request)
    {
        $notification_link_post = $this->notificationLinkPostService->getNotificationLinkPost($request);

        return $this->sendResponse(
            ['notification' => NotificationLinkPostResource::collection($notification_link_post)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($notification_link_post)]
                : []
        );
    }

    public function readNotification(Request $request)
    {
        $notificationRead = NotificationRead::updateOrCreate(
            ['notification_id' =>  $request->input('notification_id')],
            ['user_id' => auth()->user()->id]
        );
        return $this->sendResponse([
            'success' => 302,
        ]);
    }

    public function unreadNotificationCount(Request $request)
    {
        $notification = Notification::get()->count();
        $notificationRead = NotificationRead::where('user_id', auth()->user()->id)->count();
        $unreadNotification = $notification - $notificationRead;
        return $this->sendResponse([
            'unreadNotification' => $unreadNotification,
        ]);
    }

    public function clicksNotificationLinkPost(Request $request)
    {
        $notification_link_post = NotificationLinkPost::where('id', $request->input('notification_link_post_id'))->first();
        $notification_link_post->clicks = $notification_link_post->clicks + 1;
        $notification_link_post->save();

        return $this->sendResponse([
            'clicks' => $notification_link_post['clicks'],
        ]);
    }
}
