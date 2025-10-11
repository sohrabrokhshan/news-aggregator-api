<?php

namespace App\Http\Controllers\Public;

use App\Enums\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Public\ArticleService;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
    ) {}

    public function getList(Request $request): JsonResponse
    {
        return $this->sendResponse($this->articleService->getList(
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
            'resource' => ['nullable', Rule::in(Resource::values())],
            'search' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date', 'before_or_equal:today'],
            'categories' => ['nullable', 'array', 'max:20'],
            'categories.*' => ['required', 'string', 'max:255'],
            'sources' => ['nullable', 'array', 'max:20'],
            'sources.*' => ['required', 'string', 'max:255'],
            'authors' => ['nullable', 'array', 'max:20'],
            'authors.*' => ['required', 'string', 'max:255'],
        ]);
    }
}
