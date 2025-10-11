<?php

namespace App\Http\Controllers\Client;

use App\Models\Client;
use App\Rules\UniqueRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Client\UserService;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function show(): JsonResponse
    {
        return $this->sendResponse($this->userService->show(auth('client')->id()));
    }

    public function update(Request $request): JsonResponse
    {
        return $this->sendResponse(
            $this->userService->update(
                auth('client')->user(),
                $this->validateUpdate($request, auth('client')->id())
            )
        );
    }

    public function setPreferences(Request $request): JsonResponse
    {
        return $this->sendResponse(
            $this->userService->setPreferences(
                auth('client')->user(),
                $this->validateSetPreferences($request)
            )
        );
    }

    public function delete(): JsonResponse
    {
        return $this->sendResponse($this->userService->delete(auth('client')->user()));
    }

    private function validateUpdate(Request $request, int $id): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new UniqueRule(Client::class, $id)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:500'],
        ]);
    }

    private function validateSetPreferences(Request $request): array
    {
        return $request->validate([
            'sources' => ['nullable', 'array', 'max:100'],
            'sources.*' => ['string', 'max:255', 'distinct'],
            'categories' => ['nullable', 'array', 'max:100'],
            'categories.*' => ['string', 'max:255', 'distinct'],
            'authors' => ['nullable', 'array', 'max:100'],
            'authors.*' => ['string', 'max:255', 'distinct']
        ]);
    }
}
