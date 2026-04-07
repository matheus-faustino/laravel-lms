<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct() {}

    public function form()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->only('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors(__('auth.failed'));
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
