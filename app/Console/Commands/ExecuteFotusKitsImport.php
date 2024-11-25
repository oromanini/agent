<?php

namespace App\Console\Commands;

use App\Jobs\ImportFotusKitsJob;
use Illuminate\Console\Command;

class ExecuteFotusKitsImport extends Command
{
    protected $signature = 'import:fotus-kits';

    protected $description = 'Import Edeltec kits';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        (new ImportFotusKitsJob())->handle();
        $this->info('ODEX kits import started!');
    }
}
