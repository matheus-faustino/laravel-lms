<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface AuthenticationServiceInterface
{
    /**
     * Authenticate user with email and password
     *
     * @param string $email
     * @param string $password
     * @return array ['user' => User, 'token' => string]
     * @throws \Exception
     */
    public function login(string $email, string $password): array;

    /**
     * Register a new student user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User;

    /**
     * Logout user and revoke current token
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void;

    /**
     * Send password reset email
     *
     * @param string $email
     * @return void
     * @throws \Exception
     */
    public function sendResetPasswordEmail(string $email): void;

    /**
     * Reset user password with token
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function resetPassword(array $data): void;
}
