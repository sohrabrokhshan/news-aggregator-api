<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TrashModelServiceInterface extends ModelServiceInterface
{
    public function findOneTrashed(int | string $id): ?Model;

    public function getOneTrashed(int | string $id, array $relations = []): Model;

    public function getOneWithTrashed(int | string $id, array $relations = []): Model;

    public function findManyTrashed(array $ids, array $relations = []): Collection;

    public function getAllTrashed(array $relations = [], string $orderBy = 'id'): Collection;
}
