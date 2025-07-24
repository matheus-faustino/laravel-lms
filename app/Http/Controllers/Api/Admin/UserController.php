<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private UserServiceInterface $userService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'role']);
        $perPage = $request->get('per_page', 15);

        $users = $this->userService->searchUsers($filters, $perPage);

        return response()->json([
            'data' => $users
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'data' => $user
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);

        return response()->json([
            'data' => $user
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $this->userService->update($id, $request->validated());

        $user = $this->userService->findOrFail($id);

        return response()->json([
            'data' => $user
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
