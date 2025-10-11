<?php

namespace App\Services\Importers;

use App\Clients\BBCFeed;
use App\Models\Source;
use App\Enums\Resource;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BBCImporterService
{
    public function __construct(
        private readonly BBCFeed $bbcFeed,
        private readonly MetadataImporterService $metadataImporterService,
        private readonly ArticleImporterService $articleImporterService,
    ) {}

    public function importNewArticles(): void
    {
        $categories = [
            'business' => 'business',
            'politics' => 'politics',
            'health' => 'health',
            'education' => 'education',
            'science-and-environment' => 'science_and_environment',
            'entertainment-and-arts' => 'entertainment_and_arts',
            'technology' => 'technology',
        ];

        $rows = [];
        foreach ($categories as $slug => $name) {
            $rows[$slug] = $this->bbcFeed->getNewArticles($name);
        }

        $articles = $this->mapData($rows);
        DB::transaction(function () use ($articles, $categories) {
            $this->metadataImporterService->import(Category::class, $categories);
            $this->metadataImporterService->import(Source::class, ['bbc' => 'BBC']);
            $this->articleImporterService->import($articles, Resource::BBC);
        });
    }

    private function mapData(array $categoriesData): array
    {
        $articles = [];
        $now = now();

        foreach ($categoriesData as $category => $rows) {
            foreach ($rows as $row) {
                $title = Str::limit($row['title'], 255);
                if (in_array($title, array_column($articles, 'title'))) {
                    continue;
                }

                $articles[] = [
                    'slug' => Str::slug($title),
                    'category_slug' => $category,
                    'source_slug' => 'bbc',
                    'title' => $title,
                    'content' => Str::limit($row['description'], 64000),
                    'image_url' => $row['image'] ?? null,
                    'published_at' => Carbon::parse($row['pubDate']),
                    'resource_url' => $row['link'],
                    'resource' => Resource::BBC->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $articles;
    }
}
