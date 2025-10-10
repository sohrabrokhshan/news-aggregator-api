<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PaginationTrait
{
    protected static int $perPage = 10;

    public function paginateQuery(Builder $query): array
    {
        $total = $query->count();
        $items = $query->offset($this->getPaginationOffset())->limit(static::$perPage)->get();

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $this->getCurrentPage(),
                'from' => ($this->getCurrentPage() - 1) * static::$perPage + 1,
                'to' => ($this->getCurrentPage() - 1) * static::$perPage + count($items),
                'last_page' => $total === 0 ? 1 : ceil($total / static::$perPage),
                'per_page' => static::$perPage,
                'total' => $total
            ]
        ];
    }

    public function getPaginationOffset(): int
    {
        return ($this->getCurrentPage() - 1) * static::$perPage;
    }

    public function getCurrentPage(): int
    {
        return request()->query('page', 1);
    }
}
