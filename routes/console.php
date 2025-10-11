<?php

use App\Console\Commands\ImportBBCArticles;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ImportNewsApiArticles;
use App\Console\Commands\ImportGuardianArticles;

Schedule::command(ImportGuardianArticles::class)
    ->everyTenMinutes()
    ->runInBackground();

Schedule::command(ImportNewsApiArticles::class)
    ->everyTenMinutes()
    ->runInBackground();

Schedule::command(ImportBBCArticles::class)
    ->everyTenMinutes()
    ->runInBackground();
