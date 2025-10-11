<?php

namespace App\Services\Importers;

use App\Enums\Resource;
use App\Models\Article;

class ArticleImporterService
{
    public function import(array $articles, Resource $resource): void
    {
        $newRows = [];
        $existedSlugs = Article::where('resource', $resource->value)
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
