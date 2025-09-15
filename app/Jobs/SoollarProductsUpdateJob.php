<?php

namespace App\Jobs;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\SoollarApiManager;
use GuzzleHttp\Exception\RequestException; // Importe a exceção do Guzzle
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
    public int $timeout = 1800;

    public function __construct(
        private readonly ProductCategoriesEnum $category,
        private readonly WarehouseEnum $warehouse
    ){}

    public function handle(SoollarApiManager $soollarApiManager): void
    {

        try {
            $count = $soollarApiManager->handle($this->category, $this->warehouse);

            SoollarImportHistory::updateProcess(
                createdProducts: $count['created'],
                updatedProducts: $count['updated']
            );

        } catch (RequestException $e) {

            $errorMessage = sprintf(
                "Falha ao buscar produtos da categoria '%s' no armazém '%s'. Pulando esta combinação. Erro: %s",
                $this->category->value,
                $this->warehouse->value,
                $e->getMessage()
            );

            Log::warning($errorMessage);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Falha catastrófica ao executar SoollarProductsUpdateJob: ' . '\n \n'. $exception->getMessage());
        SoollarImportHistory::updateProcess(status: SoollarImportHistory::STATUS_ERROR);
    }
}
