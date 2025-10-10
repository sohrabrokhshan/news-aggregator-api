<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ModelServiceTrait
{
    public function findOne(int | string $id): ?Model
    {
        return $this->repository->query()
            ->findOne($id);
    }

    public function getOne(int | string $id, array $relations = []): Model
    {
        return $this->repository->query()
            ->with($relations)->findOrFail($id);
    }

    public function findMany(array $ids, array $relations): Collection
    {
        return $this->repository->query()
            ->with($relations)
            ->whereIn($this->getTableName() . '.' + $this->getKeyName(), $ids)->get();
    }

    public function getAll(
        array $relations = [],
        string $orderBy = 'id',
        string $orderType = 'DESC'
    ): Collection {
        return $this->repository->query()
            ->with($relations)
            ->orderBy($orderBy, $orderType)
            ->get();
    }

    private function getTableName(): string
    {
        return $this->repository->query()
            ->getModel()->getTable();
    }

    private function getKeyName(): string
    {
        return $this->repository->query()
            ->getModel()->getKeyName();
    }
}
