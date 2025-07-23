<?php

namespace App\Services;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * The repository instance
     *
     * @var BaseRepositoryInterface
     */
    protected BaseRepositoryInterface $repository;

    /**
     * BaseService constructor
     *
     * @param BaseRepositoryInterface $repository
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria): Collection
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        $this->repository->findOrFail($id);

        return $this->repository->update($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $this->repository->findOrFail($id);

        return $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $processedFilters = $this->processSearchFilters($filters);

        return $this->repository->search($processedFilters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(array $criteria): bool
    {
        return $this->repository->exists($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->repository->count();
    }

    /**
     * Process search filters for repository
     *
     * @param array $filters
     * @return array
     */
    protected function processSearchFilters(array $filters): array
    {
        return $filters;
    }
}
