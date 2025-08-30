<?php

namespace App\Jobs;

use App\Packages\SoolarApiPackage\KitsManager;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SoollarKitsUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $apiRepo = new SoollarApiRepository();
        $cableService = new CableService($apiRepo);
        (new KitsManager($apiRepo, $cableService))->handle();

        SoollarImportHistory::finishProcess();
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Falha ao executar SoollarKitsUpdateJob: ' . $exception->getMessage() . '\n \n' . $exception->getTraceAsString());
        SoollarImportHistory::updateProcess(status: SoollarImportHistory::STATUS_ERROR);
    }
}
