<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Clients\GuardianClient;
use App\Enums\Resource;
use App\Models\Article;
use Illuminate\Support\Carbon;

class GuardianArticleService
{
    public function __construct(
        private readonly GuardianClient $guardianClient,
        private readonly CategoryService $categoryService,
        private readonly SourceService $sourceService,
    ) {}

    public function importNewArticles(): void
    {
        $firstResponse = $this->guardianClient->getLatestArticles(1);
        $totalPages = $firstResponse['response']['pages'];
        $rows = $firstResponse['response']['results'];

        for ($page = 2; $page <= $totalPages; $page++) {
            $response = $this->guardianClient->getLatestArticles($page);
            $rows = array_merge($rows, $response['response']['results']);
        }

        $data = $this->extractData($rows);
        $this->categoryService->bulkInsert($data['categories']);
        $this->sourceService->bulkInsert($data['sources']);
        $this->insertNewArticles($data['articles']);
    }

    private function extractData(array $rows): array
    {
        $categories = [];
        $sources = [];
        $articles = [];
        $now = now();

        foreach ($rows as $row) {
            $categorySlug = Str::slug($row['sectionName']);
            $categories[$categorySlug] = $row['sectionName'];

            $sourceSlug = Str::slug($row['fields']['publication']);
            $sources[$sourceSlug] = $row['fields']['publication'];

            $title = Str::limit($row['webTitle'], 255);

            if (in_array($title, array_column($articles, 'title'))) {
                continue;
            }

            $articles[] = [
                'slug' => Str::slug($title),
                'category_slug' => $categorySlug,
                'source_slug' => $sourceSlug,
                'title' => $title,
                'headline' => Str::limit($row['fields']['headline'], 255),
                'content' => Str::limit($row['fields']['body'], 64000),
                'image_url' => $row['fields']['thumbnail'] ?? null,
                'published_at' => Carbon::parse($row['webPublicationDate']),
                'resource_url' => $row['webUrl'],
                'resource' => Resource::THE_GUARDING->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return [
            'categories' => $categories,
            'sources' => $sources,
            'articles' => $articles
        ];
    }

    private function insertNewArticles(array $articles): void
    {
        $newRows = [];
        $existedSlugs = Article::where('resource', Resource::THE_GUARDING->value)
            ->whereIn('slug', array_column($articles, 'slug'))
            ->select('slug')
            ->pluck('slug')
            ->toArray();

        foreach ($articles as $article) {
            if (!in_array($article['slug'], $existedSlugs)) {
                $newRows[] = $article;
            }
        }

        Article::insert($newRows);
    }
}
