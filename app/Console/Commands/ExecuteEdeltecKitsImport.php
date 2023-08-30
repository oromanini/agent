<?php

namespace App\Console\Commands;

use App\Jobs\ImportEdeltecKitsJob;
use Illuminate\Console\Command;

class ExecuteEdeltecKitsImport extends Command
{
    protected $signature = 'import:edeltec-kits';

    protected $description = 'Import Edeltec kits';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        ImportEdeltecKitsJob::dispatch();
        $this->info('Edeltec kits import started!');
    }
}
