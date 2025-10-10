<?php

namespace App\Http\Controllers\Client;

use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Client\AuthService;
use App\Rules\UniqueRule;
use App\Models\Client;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        return $this->sendResponse(
            $this->authService->register(
                $this->validateRegister($request)
            )
        );
    }

    public function login(Request $request): JsonResponse
    {
        return $this->sendResponse($this->authService->login($this->validateLogin($request)));
    }

    public function logout(): JsonResponse
    {
        return $this->sendResponse($this->authService->logout());
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $this->validateChangePassword($request);
        $this->authService->changePassword(
            auth('client')->user(),
            $data['current_password'],
            $data['new_password'],
        );
        return $this->sendResponse();
    }

    public function sendResetPasswordLink(Request $request): JsonResponse
    {
        $data = $this->validateSendResetPasswordLink($request);
        return response()->json($this->authService->sendResetPasswordLink($data['email']));
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $this->validateResetPassword($request);
        return $this->sendResponse($this->authService->resetPassword(
            $data['email'],
            $data['password'],
            $data['token']
        ));
    }

    private function validateRegister(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new UniqueRule(Client::class)],
            'password' => ['required', 'string', 'max:255', $this->getPasswordRule()],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:500'],
        ]);
    }

    private function validateLogin(Request $request): array
    {
        return $request->validate([
            'email' => ['required_without:phone', 'email'],
            'phone' => ['required_without:email', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);
    }

    private function validateChangePassword(Request $request): array
    {
        return $request->validate([
            'current_password' => ['required', 'string', 'max:255'],
            'new_password' => ['required', 'string', 'max:255', $this->getPasswordRule()],
        ]);
    }

    private function validateSendResetPasswordLink($request): array
    {
        return $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
    }

    private function validateResetPassword(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255', $this->getPasswordRule()],
            'token' => ['required', 'string', 'max:255'],
        ]);
    }

    private function getPasswordRule(): Password
    {
        return Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
    }
}
