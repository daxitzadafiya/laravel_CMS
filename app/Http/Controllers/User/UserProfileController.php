<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show()
    {
        $data = [
            'user' => new UserResource(auth()->user()),
        ];

        return $this->sendResponse($data);
    }

    public function update(UserRequest $request)
    {
        auth()->user()->update($request->validated());

        if ($request->hasFile('photo')) {
            $this->userService->addPhoto(auth()->user(), $request->file('photo'));
        }

        return $this->sendResponse([
            'message' => __('User profile updated successfully.'),
        ]);
    }
}
