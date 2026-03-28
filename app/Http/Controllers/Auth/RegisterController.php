<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(private UserServiceInterface $userService) {}

    public function form()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        Auth::login($user);

        return redirect()->route('welcome');
    }
}
