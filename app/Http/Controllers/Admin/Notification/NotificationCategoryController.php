<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationCategoryRequest;
use App\Http\Resources\NotificationCategoryResource;
use App\Models\NotificationCategory;
use App\Services\CategoryService;
use App\Http\Resources\PaginationResource;

class NotificationCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    public function index(NotificationCategoryRequest $request)
    {
        $category = $this->categoryService->getNotificationCategories($request);

        return $this->sendResponse(
            ['category' => NotificationCategoryResource::collection($category)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($category)]
                : []
        );
    }

    public function store(NotificationCategoryRequest $request)
    {
        NotificationCategory::create($request->validated());

        return $this->sendResponse([
            'message' => __('Category created successfully.'),
        ]);
    }

    public function show(NotificationCategory $category)
    {
        return $this->sendResponse([
            'category' => new NotificationCategoryResource($category),
        ]);
    }

    public function update(NotificationCategory $category, NotificationCategoryRequest $request)
    {
        $category->update($request->validated());

        return $this->sendResponse([
            'message' => __('Category updated successfully.'),
        ]);
    }

    public function destroy(NotificationCategory $category)
    {
        $category->loadCount('notifications');

        if ($category->notifications_count) {
            return $this->sendResponse([
                'message' => __('Category can not be deleted.'),
            ]);
        }

        $category->delete();

        return $this->sendResponse([
            'message' => __('Category deleted successfully.'),
        ]);
    }
}
