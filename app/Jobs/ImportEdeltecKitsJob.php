<?php

namespace App\Jobs;

use App\Packages\EdeltecApiPackage\EdeltecApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportEdeltecKitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 10800;

    public function handle(): void
    {
        (new EdeltecApiService())->importKitsFromApi();
    }
}
