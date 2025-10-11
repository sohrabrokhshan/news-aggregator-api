<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Client\ArticleService;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
    ) {}

    public function getList(Request $request): JsonResponse
    {
        return $this->sendResponse($this->articleService->getList(
            auth('client')->user(),
            $request->page_size
        ));
    }

    public function show(string $resource, string $slug): JsonResponse
    {
        $resource = str_replace('-', ' ', $resource);
        return $this->sendResponse($this->articleService->show($resource, $slug));
    }
}
