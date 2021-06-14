<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationLinkPostRequest;
use App\Http\Resources\NotificationLinkPostResource;
use App\Models\NotificationLinkPost;
use App\Services\NotificationLinkPostService;
use App\Http\Resources\PaginationResource;

class NotificationLinkPostController extends Controller
{
    protected $notificationLinkPostService;

    public function __construct(
        NotificationLinkPostService $notificationLinkPostService
    ) {
        $this->notificationLinkPostService = $notificationLinkPostService;
    }

    public function index(NotificationLinkPostRequest $request)
    {
        $notification_link_post = $this->notificationLinkPostService->getNotificationLinkPost($request);

        return $this->sendResponse(
            ['notification' => NotificationLinkPostResource::collection($notification_link_post)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($notification_link_post)]
                : []
        );
    }

    public function store(NotificationLinkPostRequest $request)
    {
        NotificationLinkPost::create($request->validated());

        return $this->sendResponse([
            'message' => __('Notification link post created successfully.'),
        ]);
    }

    public function show(NotificationLinkPost $linkPost)
    {
        return $this->sendResponse([
            'notification_link_post' => new NotificationLinkPostResource($linkPost),
        ]);
    }

    public function update(NotificationLinkPost $linkPost, NotificationLinkPostRequest $request)
    {
        $linkPost->update($request->validated());

        return $this->sendResponse([
            'message' => __('Notification link post updated successfully.'),
        ]);
    }

    public function destroy(NotificationLinkPost $linkPost)
    {
        $linkPost->delete();

        return $this->sendResponse([
            'message' => __('Notification link post deleted successfully.'),
        ]);
    }
}
