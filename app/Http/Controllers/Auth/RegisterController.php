<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(private UserServiceInterface $userService) {}

    public function form()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->createUser(array_merge(
            $request->validated(),
            ['role' => RoleEnum::USER]
        ));

        Auth::login($user);

        return redirect('/');
    }
}
