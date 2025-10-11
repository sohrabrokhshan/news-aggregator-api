<?php

namespace App\Services\Importers;

use App\Models\Source;
use App\Enums\Resource;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Clients\NewsAPIClient;
use App\Models\Author;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NewsAPIImporterService
{
    public function __construct(
        private readonly NewsAPIClient $newsApiClient,
        private readonly MetadataImporterService $metadataImporterService,
        private readonly ArticleImporterService $articleImporterService,
    ) {}

    public function importNewArticles(): void
    {
        $categories = [
            'business',
            'entertainment',
            'general',
            'health',
            'science',
            'sports',
            'technology',
        ];

        $rows = [];
        foreach ($categories as $category) {
            $rows[$category] = $this->getCategoryData($category);
        }

        $data = $this->mapData($rows);
        DB::transaction(function () use ($data, $categories) {
            $this->metadataImporterService->import(Category::class, array_combine($categories, $categories));
            $this->metadataImporterService->import(Source::class, $data['sources']);
            $this->metadataImporterService->import(Author::class, $data['authors']);
            $this->articleImporterService->import($data['articles'], Resource::NEWS_API);
        });
    }

    private function mapData(array $categoriesData): array
    {
        $sources = [];
        $authors = [];
        $articles = [];
        $now = now();

        foreach ($categoriesData as $category => $rows) {
            foreach ($rows as $row) {
                // There is a chance that the API returns null or string instead of array (I got string once)
                if (!is_array($row)) {
                    continue;
                }

                $sourceSlug = Str::slug($row['source']['name']);
                $sources[$sourceSlug] = $row['source']['name'];

                $title = Str::limit($row['title'], 255);
                $authorSlug = null;

                if (in_array($title, array_column($articles, 'title'))) {
                    continue;
                }

                if (!empty($row['author'])) {
                    $authorSlug = Str::slug($row['author']);
                    $authors[$authorSlug] = $row['author'];
                }

                $articles[] = [
                    'slug' => Str::slug($title),
                    'category_slug' => $category,
                    'source_slug' => $sourceSlug,
                    'author_slug' => $authorSlug,
                    'title' => $title,
                    'headline' => Str::limit($row['description'], 252, '...'),
                    'content' => Str::limit($row['content'], 64000),
                    'image_url' => $row['urlToImage'] ?? null,
                    'published_at' => Carbon::parse($row['publishedAt']),
                    'resource_url' => $row['url'],
                    'resource' => Resource::NEWS_API->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return [
            'sources' => $sources,
            'authors' => $authors,
            'articles' => $articles,
        ];
    }

    private function getCategoryData(string $category): array
    {
        $perPage = 100;
        $firstResponse = $this->newsApiClient->getNewArticles($category, 1, $perPage);
        $totalPages = ceil($firstResponse['totalResults'] / $perPage);
        $rows = $firstResponse['articles'];

        for ($page = 2; $page <= $totalPages; $page++) {
            $response = $this->newsApiClient->getNewArticles($category, $page, $perPage);
            $rows = array_merge($rows, $response['articles']);
        }

        return $rows;
    }
}
