<?php

namespace App\Jobs;

use App\Services\Fotus\FotusKitsImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportFotusKitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 10800;

    public function handle(): void
    {
        (new FotusKitsImportService())->importStringMonoInverterKits();
    }
}
