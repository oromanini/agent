<?php

namespace App\Jobs;

use App\Services\Odex\OdexKitsImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportOdexKitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 10800;

    public function __construct(private readonly int $limit, private readonly string $type)
    {}

    public function handle(): void
    {
        $this->type == 'microinverter'
            ? (new OdexKitsImportService())->importMicroInverterKits(limit: $this->limit)
            : (new OdexKitsImportService())->importStringMono220InverterKits()
        ;
    }
}
