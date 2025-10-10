<?php

use App\Console\Commands\ImportArticles;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ImportArticles::class, ['guardian'])
    ->everyTwoHours()
    ->runInBackground();
