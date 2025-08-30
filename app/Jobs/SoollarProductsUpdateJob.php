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

class SoollarProductsUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ProductCategoriesEnum $category,
        public WarehouseEnum $warehouse,
    ) {}

    public function handle(SoollarApiManager $soollarApiManager): void
    {
        try {
            $soollarApiManager->handle($this->category, $this->warehouse);
        } catch (\Throwable $e) {
            Log::error('Falha ao atualizar equipamentos: ' . $e->getMessage());
            SoollarImportHistory::updateProcess(status: SoollarImportHistory::STATUS_ERROR);
        }
    }
}
