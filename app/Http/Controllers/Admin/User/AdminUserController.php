<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(AdminUserRequest $request)
    {
        $adminUsers = $this->userService->getAdminUsers($request);

        return $this->sendResponse(
            ['admins' => UserResource::collection($adminUsers)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($adminUsers)]
                : []
        );
    }

    public function store(AdminUserRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'A';

        User::create($data);

        return $this->sendResponse([
            'message' => __('Admin created successfully.'),
        ]);
    }

    public function show(User $admin)
    {
        abort_if($admin->role != 'A', 404);

        return $this->sendResponse([
            'admin' => new UserResource($admin),
        ]);
    }

    public function update(User $admin, AdminUserRequest $request)
    {
        abort_if($admin->role != 'A', 404);

        $admin->update($request->validated());

        return $this->sendResponse([
            'message' => __('Admin updated successfully.'),
        ]);
    }
}
