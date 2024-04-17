<?php

namespace App\Console\Commands;

use App\Jobs\ImportOdexKitsJob;
use Illuminate\Console\Command;

class ExecuteOdexKitsImport extends Command
{
    protected $signature = 'import:odex-kits {limit?}';

    protected $description = 'Import Odex kits';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $limit = $this->argument('limit') ?? 200;
        (new ImportOdexKitsJob($limit))->handle();
        $this->info('ODEX kits import started!');
    }
}
