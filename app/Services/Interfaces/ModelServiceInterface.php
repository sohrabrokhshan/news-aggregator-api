<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ModelServiceInterface
{
    public function findOne(int | string $id): ?Model;

    public function getOne(int | string $id, array $relations = []): Model;

    public function findMany(array $ids, array $relations): Collection;

    public function getAll(
        array $relations = [],
        string $orderBy = 'id',
        string $orderType = 'DESC'
    ): Collection;
}
