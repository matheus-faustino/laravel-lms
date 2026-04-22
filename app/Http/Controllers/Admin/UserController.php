<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserServiceInterface $userService) {}

    public function index(Request $request)
    {
        $users = $this->userService->getPaginatedUsers($request->query('perPage'), ['role' => RoleEnum::USER], ['id', 'name', 'email', 'created_at', 'updated_at']);

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser(array_merge($request->validated(), ['role' => RoleEnum::USER]));

        return to_route('admin.users.index')->with('success', __('admin/users.created_success'));
    }

    public function edit(int $userId)
    {
        $user = $this->userService->getUser($userId);

        return view('admin.user.edit', compact('user'));
    }

    public function update(int $userId, UpdateUserRequest $request)
    {
        $this->userService->updateUser($userId, $request->validated());

        return to_route('admin.users.index')->with('success', __('admin/users.updated_success'));
    }

    public function delete(int $userId)
    {
        $this->userService->deleteUser($userId);

        return response()->json(['message' => __('admin/users.deleted_success')]);
    }
}
