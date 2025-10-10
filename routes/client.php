<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client;

Route::prefix('auth')
    ->name('auth.')
    ->controller(Client\AuthController::class)
    ->group(function () {
        Route::withoutMiddleware('auth:client')
            ->group(function () {
                Route::post('register', 'register');
                Route::post('login', 'login');
                Route::post('send-reset-password-link', 'sendResetPasswordLink');
                Route::post('reset-password', 'resetPassword');
            });

        Route::post('logout', 'logout');
        Route::post('change-password', 'changePassword');
    });
