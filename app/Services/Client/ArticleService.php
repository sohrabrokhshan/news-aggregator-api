<?php

namespace App\Services\Client;

use App\Models\Client;
use App\Models\Article;
use App\Services\Utils\Pagination;

class ArticleService
{
    public function __construct(
        private readonly Article $repository,
        private readonly Pagination $pagination,
    ) {}

    public function getList(Client $client, array $filters, ?int $pageSize): array
    {
        $sources = $client->preferences?->sources ?? [];
        $categories = $client->preferences?->categories ?? [];
        $authors = $client->preferences?->authors ?? [];

        $query = $this->repository
            ->when(!empty($sources), fn($q) => $q->whereIn('source_slug', $sources))
            ->when(!empty($categories), fn($q) => $q->orWhereIn('category_slug', $categories))
            ->when(!empty($authors), fn($q) => $q->orWhereIn('author_slug', $authors))
            ->when(
                !empty($filters['start_date']),
                fn($q) => $q->whereDate('published_at', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn($q) => $q->whereDate('published_at', '<', $filters['end_date'])
            )
            ->with(['author', 'category', 'source'])
            ->orderBy('published_at', 'DESC');

        return $this->pagination->setQuery($query)->paginate($pageSize);
    }

    public function show(string $resource, string $slug): Article
    {
        return $this->repository->where('resource', $resource)
            ->where('slug', $slug)
            ->with(['category', 'source', 'author'])
            ->firstOrFail();
    }
}
