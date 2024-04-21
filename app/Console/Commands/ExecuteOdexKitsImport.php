<?php

namespace App\Console\Commands;

use App\Jobs\ImportOdexKitsJob;
use Illuminate\Console\Command;

class ExecuteOdexKitsImport extends Command
{
    protected $signature = 'import:odex-kits {limit?} {type?}';

    protected $description = 'Import Odex kits';

    protected const MICRO = 'microinverter';
    protected const STRING = 'stringinverter';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $limit = $this->argument('limit') ?? 200;
        $type = $this->argument('type') == 'microinverter' ? self::MICRO : self::STRING ;
        (new ImportOdexKitsJob($limit, $type))->handle();
        $this->info('ODEX kits import started!');
    }
}
