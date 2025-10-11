<?php

namespace App\Services\Public;

use App\Models\Article;
use App\Services\Utils\Pagination;

class ArticleService
{
    public function __construct(
        private readonly Article $repository,
        private readonly Pagination $pagination,
    ) {}

    public function getList(array $filters, ?int $pageSize): array
    {
        $query = $this->repository
            ->with(['author', 'category', 'source'])
            ->orderBy('published_at', 'DESC');

        if (!empty($filters['categories'])) {
            $query->whereIn('category_slug', $filters['categories']);
        }

        if (!empty($filters['sources'])) {
            $query->whereIn('source_slug', $filters['sources']);
        }

        if (!empty($filters['authors'])) {
            $query->whereIn('author_slug', $filters['authors']);
        }

        if (!empty($filters['search'])) {
            $query->whereFullText(['title', 'headline', 'content'], $filters['search']);
        }

        if (!empty($filters['resource'])) {
            $query->where('resource', $filters['resource']);
        }

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
