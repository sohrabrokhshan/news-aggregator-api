<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Importers\NewsAPIImporterService;

class ImportNewsApiArticles extends Command
{
    protected $signature = 'app:import-news-api-articles';
    protected $description = 'Import new articles from the news api';

    public function __construct(
        private readonly NewsAPIImporterService $importerService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->importerService->importNewArticles();
    }
}
