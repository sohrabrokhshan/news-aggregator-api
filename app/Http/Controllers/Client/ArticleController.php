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
            $this->validateGetList($request),
            $request->page_size
        ));
    }

    public function show(string $resource, string $slug): JsonResponse
    {
        $resource = str_replace('-', ' ', $resource);
        return $this->sendResponse($this->articleService->show($resource, $slug));
    }

    private function validateGetList(Request $request): array
    {
        return $request->validate([
            'start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date', 'before_or_equal:today'],
        ]);
    }
}
