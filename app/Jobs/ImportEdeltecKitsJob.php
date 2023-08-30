<?php

namespace App\Jobs;

use App\Packages\EdeltecApiPackage\EdeltecApiService;
use App\Packages\EdeltecApiPackage\Exceptions\EdeltecApiSearchFailException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportEdeltecKitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    /** @throws GuzzleException|EdeltecApiSearchFailException */
    public function handle(): void
    {
        (new EdeltecApiService(new Client()))
            ->importKitsFromGateway();
    }
}
