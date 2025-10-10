<?php

return [
    'clients' => [
        'guardian' => [
            'url' => env('GUARDIAN_URL'),
            'api-key' => env('GUARDIAN_API_KEY'),
        ],
        'news-api' => [
            'url' => env('NEWSAPI_URL'),
            'api-key' => env('NEWSAPI_API_KEY'),
        ],
    ],
];
