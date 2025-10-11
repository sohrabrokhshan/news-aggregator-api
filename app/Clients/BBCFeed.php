<?php

namespace App\Clients;

class BBCFeed extends RSSFeed
{
    public function __construct()
    {
        $this->url = config('general.clients.bbc-feeds.url');
    }

    public function getNewArticles(string $category): array
    {
        $xml = $this->sendRequest("$category/rss.xml");
        $items = $xml->channel->item ?? [];

        $rows = [];
        foreach ($items as $item) {
            $image = $item->children('media', true)->thumbnail->attributes()->url;

            $rows[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'description' => (string) $item->description,
                'pubDate' => (string) ($item->pubDate ?? 'now'),
                'image' => $image ? (string) $image : null,
                'source' => 'bbc'
            ];
        }

        return $rows;
    }
}
