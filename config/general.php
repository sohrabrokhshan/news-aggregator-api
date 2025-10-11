<?php

return [
    'client_app_url' => env('CLIENT_APP_URL'),
    'clients' => [
        'guardian' => [
            'url' => env('GUARDIAN_URL'),
            'api-key' => env('GUARDIAN_API_KEY'),
        ],
        'news-api' => [
            'url' => env('NEWSAPI_URL'),
            'api-key' => env('NEWSAPI_API_KEY'),
        ],
        'bbc-feeds' => [
            'url' => env('BBC_FEEDS_URL'),
        ],
    ],
];
