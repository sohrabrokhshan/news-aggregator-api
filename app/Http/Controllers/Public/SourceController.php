<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Public\SourceService;

class SourceController extends Controller
{
    public function __construct(
        private readonly SourceService $sourceService,
    ) {}

    public function searchList(Request $request): JsonResponse
    {
        $data = $this->validateSearchList($request);
        return $this->sendResponse($this->sourceService->searchList->search($data));
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
