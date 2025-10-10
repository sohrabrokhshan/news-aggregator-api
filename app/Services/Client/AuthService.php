<?php

namespace App\Services\Client;

use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Auth\Events\PasswordReset;
use App\Exceptions\AuthenticationFailedException;
use App\Models\Client;

class AuthService
{
    private JWTGuard $guard;
    private PasswordBroker $passwordBroker;

    public function __construct(
        private readonly UserService $userService,
    ) {
        $this->guard = Auth::guard('client');
        $this->passwordBroker = Password::broker('clients');
    }

    public function register(array $data): array
    {
        $user = $this->userService->create($data);
        return [
            'user' => $user,
            'token' => $this->createToken($user),
        ];
    }

    public function login(array $data): array
    {
        $user = $this->userService->findByEmail($data['email']);

        if (is_null($user) || Hash::check($data['password'], $user->password) === false) {
            throw new AuthenticationFailedException();
        }

        return [
            'user' => $user,
            'token' => $this->createToken($user),
        ];
    }

    public function logout(): void
    {
        $this->guard->logout(true);
    }

    public function changePassword(
        Client $user,
        string $currentPass,
        string $newPass
    ): void {
        if (Hash::check($currentPass, $user->password) === false) {
            throw ValidationException::withMessages(['*' => 'Password is incorrect.']);
        }

        $user->password = Hash::make($newPass);
        $user->save();
    }

    public function sendResetPasswordLink(string $email): bool
    {
        $status = $this->passwordBroker->sendResetLink(['email' => $email]);
        return $status === Password::RESET_LINK_SENT;
    }

    public function resetPassword(
        string $email,
        string $password,
        string $token
    ): bool | array {
        $status = $this->passwordBroker->reset(
            ['email' => $email, 'password' => $password, 'token' => $token],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::INVALID_USER) {
            throw ValidationException::withMessages(['*' => 'Invalid credentials.']);
        }

        if ($status === Password::INVALID_TOKEN) {
            throw ValidationException::withMessages(['*' => 'Invalid token.']);
        }

        $user = $this->userService->findByEmail($email);
        return $status === Password::PASSWORD_RESET ? [
            'user' => $user,
            'token' => $this->createToken($user),
        ] : false;
    }

    public function createToken(JWTSubject $user): string
    {
        return $this->guard
            ->setTTL((int) config('jwt.ttl'))
            ->login($user);
    }
}
