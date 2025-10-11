<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public;

Route::prefix('sources')
    ->controller(Public\SourceController::class)
    ->group(function () {
        Route::get('search-list', 'searchList');
    });

Route::prefix('authors')
    ->controller(Public\AuthorController::class)
    ->group(function () {
        Route::get('search-list', 'searchList');
    });

Route::prefix('categories')
    ->controller(Public\CategoryController::class)
    ->group(function () {
        Route::get('/', 'getList');
        Route::get('{slug}', 'show');
    });

Route::prefix('articles')
    ->controller(Public\ArticleController::class)
    ->group(function () {
        Route::get('/', 'getList');
        Route::get('{resource}/{slug}', 'show');
    });
