<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Importers\GuardianImporterService;

class ImportGuardianArticles extends Command
{
    protected $signature = 'app:import-guardian-articles';
    protected $description = 'Import new articles from the guardian';

    public function __construct(
        private readonly GuardianImporterService $importerService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Importing new articles from the guardian.');
        $this->importerService->importNewArticles();
        $this->info('Importing articles from the guardian completed.');
    }
}
