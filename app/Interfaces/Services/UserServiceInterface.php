<?php

namespace App\Interfaces\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    /**
     * Retrieve all users from the database.
     *
     * @param array $columns The columns to retrieve.
     * @return Collection<int, User>
     */
    public function getAllUsers(array $columns = ['*']): Collection;

    /**
     * Retrieve a single user by ID.
     *
     * @param int   $id      The user ID.
     * @param array $colums  The columns to retrieve.
     * @return ?User
     */
    public function getUser(int $id, array $colums = ['*']): ?User;

    /**
     * Create a new user with the given attributes.
     *
     * @param array $attributes The user attributes.
     * @return User
     */
    public function createUser(array $attributes = []): User;

    /**
     * Update an existing user by ID.
     *
     * @param int   $id         The user ID.
     * @param array $attributes The attributes to update.
     * @return User
     */
    public function updateUser(int $id, array $attributes = []): User;

    /**
     * Delete a user by ID.
     *
     * @param int $id The user ID.
     * @return bool True on success, false otherwise.
     */
    public function deleteUser(int $id): bool;

    /**
     * Get users count
     * 
     * @param array $criteria
     * @return int
     */
    public function getUsersCount(array $criteria = []): int;

    /**
     * Get paginated users based on given criteria
     * 
     * @param array $criteria
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(?int $perPage = 10, ?array $criteria = [], ?array $colums = ['*']): LengthAwarePaginator;
}
