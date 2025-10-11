<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Public\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function getList(): JsonResponse
    {
        return $this->sendResponse($this->categoryService->getList());
    }

    public function show(string $slug): JsonResponse
    {
        return $this->sendResponse($this->categoryService->show($slug));
    }
}
