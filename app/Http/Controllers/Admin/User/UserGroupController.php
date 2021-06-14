<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserGroupRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UserGroupResource;
use App\Models\UserGroup;
use App\Services\UserGroupService;

class UserGroupController extends Controller
{
    protected $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    public function index(UserGroupRequest $request)
    {
        $groups = $this->userGroupService->getGroups($request);

        return $this->sendResponse(
            ['groups' => UserGroupResource::collection($groups)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($groups)]
                : []
        );
    }

    public function store(UserGroupRequest $request)
    {
        UserGroup::create($request->validated());

        return $this->sendResponse([
            'message' => __('Group created successfully.'),
        ]);
    }

    public function show(UserGroup $userGroup)
    {
        return $this->sendResponse([
            'group' => new UserGroupResource($userGroup),
        ]);
    }

    public function update(UserGroup $userGroup, UserGroupRequest $request)
    {
        $userGroup->update($request->validated());

        return $this->sendResponse([
            'message' => __('Group updated successfully.'),
        ]);
    }

    public function destroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        return $this->sendResponse([
            'message' => __('Group deleted successfully.'),
        ]);
    }
}
