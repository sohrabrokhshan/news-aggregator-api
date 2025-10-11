<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ImportNewsApiArticles;
use App\Console\Commands\ImportGuardianArticles;

Schedule::command(ImportGuardianArticles::class)
    ->everyTwoHours()
    ->runInBackground();

Schedule::command(ImportNewsApiArticles::class)
    ->everyTwoHours()
    ->runInBackground();
