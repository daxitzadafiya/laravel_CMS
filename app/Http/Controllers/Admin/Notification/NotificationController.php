<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Events\SendBrowserNotify;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Http\Resources\PaginationResource;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(
        NotificationService $notificationService
    ) {
        $this->notificationService = $notificationService;
    }

    public function index(NotificationRequest $request)
    {
        // dd($request->all());
        $notification = $this->notificationService->getNotifications($request);

        return $this->sendResponse(
            ['notification' => NotificationResource::collection($notification)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($notification)]
                : []
        );
    }

    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());

        if ($request->input('tags')) {
            $tags = $this->notificationService->getTagIds($request->input('tags'));
            $notification->tags()->sync($tags);
        }

        if ($request->input('group')) {
            $notification->userGroups()->sync($request->input('group'));
        }

        $data = array(
            'title' => $notification->title, // Required
            'message' =>  $notification->description, // Required
            'url' => env('FRONTEND_SITE', 'https://cp-front.motocle8.com/') . 'notifications/' . $notification->id, // Required
            'notification' => $notification // Required
        );
        event(new SendBrowserNotify($data));

        return $this->sendResponse([
            'message' => __('Notification created successfully.'),
        ]);
    }

    public function show(Notification $notification)
    {
        $notification->load('category', 'tags', 'userGroups');
        $notification->type = collect(config('reddish.notification.types'))->where('id', $notification->type_id)->first();

        return $this->sendResponse([
            'notification' => new NotificationResource($notification),
        ]);
    }

    public function update(Notification $notification, NotificationRequest $request)
    {
        $notification->update($request->validated());

        if ($request->input('tags')) {
            $tags = $this->notificationService->getTagIds($request->input('tags'));
            $notification->tags()->sync($tags);
        }

        if ($request->input('group')) {
            $notification->userGroups()->sync($request->input('group'));
        }

        return $this->sendResponse([
            'message' => __('Notification updated successfully.'),
        ]);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return $this->sendResponse([
            'message' => __('Notification deleted successfully.'),
        ]);
    }
}
