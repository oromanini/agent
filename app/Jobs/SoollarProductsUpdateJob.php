<?php

namespace App\Jobs;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\SoollarApiManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SoollarProductsUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutos

    public function handle(SoollarApiManager $soollarApiManager): void
    {
        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                $soollarApiManager->handle($category, $warehouse);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Falha ao executar SoollarProductsUpdateJob: ' . '\n \n'. $exception->getMessage());
    }
}
