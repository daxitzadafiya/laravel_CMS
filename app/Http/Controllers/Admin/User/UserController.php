<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailWelcomeUser;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(UserRequest $request)
    {
        $users = $this->userService->getUsers($request);

        return $this->sendResponse(
            ['users' => UserResource::collection($users)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($users)]
                : []
        );
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'U';

        $user = User::create($data);

        if ($request->input('groups')) {
            $user->userGroups()->sync($request->input('groups'));
        }

        if ($request->hasFile('photo')) {
            $this->userService->addPhoto($user, $request->file('photo'));
        }

        if (!empty ($data['notification_email'])) {
            Mail::to($user->email)
                ->send(new EmailWelcomeUser($data));
        }

        return $this->sendResponse([
            'message' => __('User created successfully.'),
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'company.prefecture',
            'company.headCount',
            'userGroups'
        ]);

        return $this->sendResponse([
            'user' => new UserResource($user),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());

        if ($request->input('group')) {
            $user->userGroups()->sync($request->input('group'));
        }

        if ($request->hasFile('photo')) {
            $this->userService->addPhoto($user, $request->file('photo'));
        }

        return $this->sendResponse([
            'message' => __('User updated successfully.'),
        ]);
    }
}
