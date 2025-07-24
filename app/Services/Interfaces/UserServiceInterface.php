<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface extends BaseServiceInterface
{
    /**
     * Create a new user with encrypted password
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User;

    /**
     * Update user profile
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool;

    /**
     * Change user password
     *
     * @param int $id
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws \Exception
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): bool;

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get users by role
     *
     * @param string $role
     * @return Collection
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Search users with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchUsers(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if email is available for user
     *
     * @param string $email
     * @param int|null $excludeUserId
     * @return bool
     */
    public function isEmailAvailable(string $email, ?int $excludeUserId = null): bool;
}
