<?php

namespace App\Clients;

class GuardianClient extends Client
{
    public function __construct()
    {
        $this->url = config('general.clients.guardian.url');
        $this->apiKey = config('general.clients.guardian.api-key');
    }

    public function getLatestArticles(int $page): array
    {
        return $this->sendGet('search', [
            'lang' => 'en',
            'type' => 'article',
            'from-date' => now()->toDateString(),
            'use-date' => 'published',
            'page-size' => 10,
            'page' => $page,
            'order-by' => 'newest',
            'order-date' => 'published',
            'show-fields' => 'body,headline,thumbnail,tag,publication',
        ]);
    }
}
