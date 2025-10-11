<?php

namespace App\Console\Commands;

use App\Services\Importers\BBCImporterService;
use Illuminate\Console\Command;

class ImportBBCArticles extends Command
{
    protected $signature = 'app:import-bbc-articles';
    protected $description = 'Import new articles from BBC feeds';

    public function __construct(
        private readonly BBCImporterService $importerService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Importing new articles from the BBC feeds.');
        $this->importerService->importNewArticles();
        $this->info('Importing articles from the BBC feeds completed.');
    }
}
