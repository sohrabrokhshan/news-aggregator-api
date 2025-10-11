<?php

use Illuminate\Support\Facades\Route;

Route::prefix('client')
    ->middleware(['auth:client'])
    ->group(base_path('routes/client.php'));

Route::prefix('/')
    ->group(base_path('routes/public.php'));
