<?php

namespace App\Services\Public;

use App\Models\Source;
use App\Services\Utils\SearchList;

class SourceService
{
    public SearchList $searchList;

    public function __construct(
        private readonly Source $repository,
    ) {
        $this->searchList = new SearchList('name', $this->repository);
    }
}
