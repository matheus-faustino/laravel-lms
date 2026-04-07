<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Interfaces\Services\UserServiceInterface;

class DashboardController extends Controller
{
    public function __construct(private UserServiceInterface $userService) {}

    public function index()
    {
        $usersCount = $this->userService->getUsersCount(['role' => RoleEnum::USER]);

        return view('admin.dashboard.index', compact('usersCount'));
    }
}
