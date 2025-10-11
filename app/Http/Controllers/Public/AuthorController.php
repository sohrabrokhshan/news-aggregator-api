<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Public\AuthorService;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService,
    ) {}

    public function searchList(Request $request): JsonResponse
    {
        $data = $this->validateSearchList($request);
        return $this->sendResponse($this->authorService->searchList->search($data));
    }

    private function validateSearchList(Request $request): array
    {
        return $request->validate([
            'term' => ['nullable', 'string', 'max:255'],
            'ids' => ['nullable', 'array', 'max:100'],
            'ids.*' => ['required', 'string'],
        ]);
    }
}
