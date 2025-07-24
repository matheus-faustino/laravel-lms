<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(private UserServiceInterface $userService) {}

    public function login(string $email, string $password): array
    {
        $user = $this->userService->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function register(array $data): User
    {
        $data['role'] = User::ROLE_STUDENT;

        return $this->userService->createUser($data);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function sendResetPasswordEmail(string $email): void
    {
        $user = $this->userService->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Failed to send reset link.');
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception(__($status));
        }
    }
}
