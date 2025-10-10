<?php

namespace App\Services\Utils;

use Illuminate\Database\Eloquent\Builder;

class Pagination
{
    private Builder $query;

    public function __construct() {}

    public function paginate(?int $perPage = null): array
    {
        $perPage = $perPage && $perPage <= 100 ? $perPage : 10;
        $total = $this->query->count();
        $items = $this->query->offset($this->getPaginationOffset($perPage))->limit($perPage)->get();

        $pagination = [
            'data' => $items,
            'meta' => [
                'current_page' => $this->getCurrentPage(),
                'from' => ($this->getCurrentPage() - 1) * $perPage + 1,
                'to' => ($this->getCurrentPage() - 1) * $perPage + count($items),
                'last_page' => $total === 0 ? 1 : ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ];
        $this->query = $this->query->getModel()->query();

        return $pagination;
    }

    public function filter(array $filters, array $operators = []): self
    {
        foreach ($filters as $column => $value) {
            $operator = $operators[$column] ?? '=';
            $value = $value;

            if ($operator === 'like') {
                $value = "%$value";
            } else if ($operator === 'match') {
                $operator = 'like';
                $value = "%$value%";
            }

            $this->query->where($column, $operator, $value);
        }

        return $this;
    }

    public function setQuery(Builder $query): self
    {
        $this->query = $query;
        return $this;
    }

    private function getPaginationOffset(int $perPage): int
    {
        return ($this->getCurrentPage() - 1) * $perPage;
    }

    private function getCurrentPage(): int
    {
        return request('page', 1);
    }
}
