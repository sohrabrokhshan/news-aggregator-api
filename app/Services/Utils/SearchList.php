<?php

namespace App\Services\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class SearchList
{
    private ?Closure $applySearchTerm = null;
    private ?Closure $applySearchFilter = null;

    public function __construct(
        private readonly string $textAlias,
        private readonly Model $repository,
        private readonly int $limit = 10,
    ) {}

    public function search(array $filter): Collection
    {
        return $this->searchQuery($this->repository->query(), $filter);
    }

    public function setApplySearchTerm(Closure $closure): void
    {
        $this->applySearchTerm = $closure;
    }

    public function setApplySearchFilter(Closure $closure): void
    {
        $this->applySearchFilter = $closure;
    }

    public function searchQuery(Builder $query, array $filter): Collection
    {
        $limit = $this->limit;
        $keyName = $this->getKeyName();

        if (!empty($filter['term'])) {
            if ($this->applySearchTerm) {
                ($this->applySearchTerm)($query, $filter['term']);
            } else {
                $query->whereRaw($this->textAlias . ' like ?', ["%{$filter['term']}%"]);
            }
        }

        if (!empty($filter['ids'])) {
            $idsCount = count(array_unique($filter['ids']));
            $limit = max($idsCount, $limit);
            $query->whereIn($keyName, $filter['ids']);
        }

        unset($filter['term'], $filter['id'], $filter['ids']);

        if ($this->applySearchFilter) {
            ($this->applySearchFilter)($query, $filter);
        } else {
            $query->where($filter);
        }

        $query = $query->select("$keyName as id")
            ->addSelect(DB::raw($this->textAlias . ' AS text'));

        return $query
            ->limit($limit > 100 ? 100 : $limit)
            ->orderBy('text')
            ->get();
    }

    private function getKeyName(): string
    {
        $model = $this->repository->query()->getModel();
        return $model->getTable() . '.' . $model->getKeyName();
    }
}
