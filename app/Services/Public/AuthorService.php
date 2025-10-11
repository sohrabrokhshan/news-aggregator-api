<?php

namespace App\Services\Public;

use App\Models\Author;
use App\Services\Utils\SearchList;

class AuthorService
{
    public SearchList $searchList;

    public function __construct(
        private readonly Author $repository,
    ) {
        $this->searchList = new SearchList('name', $this->repository);
    }
}
