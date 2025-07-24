<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProfile(int $id, array $data): bool
    {
        unset($data['password'], $data['email']);

        return $this->update($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $user = $this->repository->findOrFail($id);

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        return $this->repository->update($id, [
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->repository->getByRole($role);
    }

    /**
     * {@inheritDoc}
     */
    public function searchUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchUsers($filters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmailAvailable(string $email, ?int $excludeUserId = null): bool
    {
        return !$this->repository->emailExists($email, $excludeUserId);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->repository->findOrFail($id);

        return $this->repository->update($id, $data);
    }
}
