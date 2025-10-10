<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GuardianArticleService;

class ImportArticles extends Command
{
    protected $signature = 'app:import-articles {source}';
    protected $description = 'Command description';

    public function __construct(private readonly GuardianArticleService $guardianArticleService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $source = $this->argument('source');
        $this->info("source is {$source}!");

        if ($source === 'guardian') {
            $this->guardianArticleService->importNewArticles();
        }
    }
}
