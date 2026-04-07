<?php

namespace App\Services;

use App\Interfaces\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    /** @inheritDoc */
    public function getAllUsers(array $colums = ['*']): Collection
    {
        return User::query()->get($colums);
    }

    /** @inheritDoc */
    public function getUser(int $id, array $colums = ['*']): ?User
    {
        return User::query()->find($id, $colums);
    }

    /** @inheritDoc */
    public function createUser(array $attributes = []): User
    {
        return DB::transaction(function () use ($attributes): User {
            return User::query()->create($attributes);
        });
    }

    /** @inheritDoc */
    public function updateUser(int $id, array $attributes = []): User
    {
        return DB::transaction(function () use ($id, $attributes): User {
            $user = User::query()->findOrFail($id);

            $user->update($attributes);

            return $user->fresh();
        });
    }

    /** @inheritDoc */
    public function deleteUser(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            return User::query()->findOrFail($id)->delete();
        });
    }

    /** @inheritDoc */
    public function getUsersCount(array $criteria = []): int
    {
        return User::query()->where($criteria)->count();
    }
}
