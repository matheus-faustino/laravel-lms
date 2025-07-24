<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Interfaces\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(private AuthenticationServiceInterface $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated('email'),
                $request->validated('password')
            );

            return response()->json([
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'data' => $user,
            'message' => 'Registration successful'
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendResetPasswordEmail($request->validated('email'));

        return response()->json([
            'message' => 'Password reset email sent successfully'
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->validated());

            return response()->json([
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
        ]);
    }
}
