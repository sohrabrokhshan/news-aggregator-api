<?php

namespace App\Services\Public;

use App\Models\Category;
use App\Services\Traits\ModelServiceTrait;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Interfaces\ModelServiceInterface;

class CategoryService implements ModelServiceInterface
{
    use ModelServiceTrait;

    public function __construct(private readonly Category $repository) {}

    public function getList(): Collection
    {
        return $this->getAll([], 'name', 'ASC');
    }

    public function show(string $slug): Category
    {
        return $this->getOne($slug);
    }
}
