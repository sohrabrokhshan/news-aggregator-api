<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait TrashModelServiceTrait
{
    use ModelServiceTrait;

    public function findOneTrashed(int | string $id): ?Model
    {
        return $this->repository->query()
            ->onlyTrashed()->findOne($id);
    }

    public function getOneTrashed(int | string $id, array $relations = []): Model
    {
        return $this->repository->query()
            ->onlyTrashed()->with($relations)->findOrFail($id);
    }

    public function getOneWithTrashed(int | string $id, array $relations = []): Model
    {
        return $this->repository->query()
            ->withTrashed()->with($relations)->findOrFail($id);
    }

    public function findManyTrashed(array $ids, array $relations = []): Collection
    {
        return $this->repository->query()
            ->onlyTrashed()
            ->with($relations)
            ->whereIn($this->getTableName() . '.' + $this->getKeyName(), $ids)
            ->get();
    }

    public function getAllTrashed(
        array $relations = [],
        string $orderBy = 'id',
        string $orderType = 'DESC'
    ): Collection {
        return $this->repository->query()
            ->onlyTrashed()->with($relations)
            ->orderBy($orderBy, $orderType)
            ->get();
    }
}
