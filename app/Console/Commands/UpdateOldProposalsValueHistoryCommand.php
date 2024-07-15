<?php

namespace App\Console\Commands;

use App\Jobs\UpdateProposalsValueHistoryJob;
use Illuminate\Console\Command;

class UpdateOldProposalsValueHistoryCommand extends Command
{
    protected $signature = 'update:value-histories';

    protected $description = 'Update old proposals value history';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        UpdateProposalsValueHistoryJob::dispatch();
        $this->info('Old proposals value history update started!');
    }
}
