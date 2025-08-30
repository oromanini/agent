<?php

namespace App\Jobs;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class StartSoollarUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        SoollarImportHistory::initProcess();

        $jobsToChain = [];
        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                $jobsToChain[] = new SoollarProductsUpdateJob($category, $warehouse);
            }
        }

        $jobsToChain[] = new SoollarKitsUpdateJob();

        Bus::chain($jobsToChain)->dispatch();
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Falha ao iniciar o processo de atualização Soollar: ' . $exception->getMessage());
        SoollarImportHistory::updateProcess(status: SoollarImportHistory::STATUS_ERROR);
    }
}
