<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationTagRequest;
use App\Http\Resources\NotificationTagResource;
use App\Http\Resources\PaginationResource;
use App\Models\NotificationTag;
use App\Services\TagService;

class NotificationTagController extends Controller
{
    protected $tagService;

    public function __construct(
        TagService $tagService
    ) {
        $this->tagService = $tagService;
    }

    public function index(NotificationTagRequest $request)
    {
        $tags = $this->tagService->getNotificationTags($request);

        return $this->sendResponse(
            ['tags' => NotificationTagResource::collection($tags)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($tags)]
                : []
        );
    }

    public function store(NotificationTagRequest $request)
    {
        NotificationTag::create($request->validated());

        return $this->sendResponse([
            'message' => __('Tags created successfully.'),
        ]);
    }

    public function show(NotificationTag $tag)
    {
        return $this->sendResponse([
            'tags' => new NotificationTagResource($tag),
        ]);
    }

    public function update(NotificationTag $tag, NotificationTagRequest $request)
    {
        $tag->update($request->validated());

        return $this->sendResponse([
            'message' => __('Tag updated successfully.'),
        ]);
    }

    public function destroy(NotificationTag $tag)
    {
        $tag->delete();

        return $this->sendResponse([
            'message' => __('Tag deleted successfully.'),
        ]);
    }
}
