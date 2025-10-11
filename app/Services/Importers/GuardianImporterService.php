<?php

namespace App\Services\Importers;

use App\Models\Source;
use App\Enums\Resource;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Clients\GuardianClient;
use Illuminate\Support\Facades\DB;

class GuardianImporterService
{
    public function __construct(
        private readonly GuardianClient $guardianClient,
        private readonly MetadataImporterService $metadataImporterService,
        private readonly ArticleImporterService $articleImporterService,
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

        $data = $this->mapData($rows);
        DB::transaction(function () use ($data) {
            $this->metadataImporterService->import(Category::class, $data['categories']);
            $this->metadataImporterService->import(Source::class, $data['sources']);
            $this->articleImporterService->import($data['articles'], Resource::THE_GUARDING);
        });
    }

    private function mapData(array $rows): array
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
}
