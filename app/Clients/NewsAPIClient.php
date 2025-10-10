<?php

namespace App\Clients;

class NewsAPIClient extends Client
{
    public function __construct()
    {
        $this->url = config('general.clients.news-api.url');
        $this->apiKey = config('general.clients.news-api.api-key');
    }

    public function getNewArticles(string $category, int $page, int $perPage): array
    {
        return $this->sendGet('top-headlines', [
            'apiKey' => $this->apiKey,
            'language' => 'en',
            'from' => now()->toDateString(),
            'sortBy' => 'publishedAt',
            'page' => $page,
            'pageSize' => $perPage,
            'category' => $category,
        ]);
    }
}
