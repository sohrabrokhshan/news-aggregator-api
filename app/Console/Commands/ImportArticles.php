<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAPIArticleService;
use App\Services\GuardianArticleService;

class ImportArticles extends Command
{
    protected $signature = 'app:import-articles {source}';
    protected $description = 'Import new articles from external sources';

    public function __construct(
        private readonly GuardianArticleService $guardianArticleService,
        private readonly NewsAPIArticleService $newsAPIArticleService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $source = $this->argument('source');
        $this->info("source is {$source}!");

        if ($source === 'guardian') {
            $this->guardianArticleService->importNewArticles();
        }

        if ($source = 'news-api') {
            $this->newsAPIArticleService->importNewArticles();
        }
    }
}
