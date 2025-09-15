<?php

namespace App\Packages\SoolarApiPackage\Services;

use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use Exception;

class CableService
{

    public function __construct(private readonly SoollarApiRepository $soollarApiRepository)
    {}

    public function getBestCableOption(int $moduleQuantity, string $color): array
    {
        $requiredLength = $moduleQuantity * 2.5;
        $model = $moduleQuantity > 30 ? '6MM' : '4MM';

        // Obtém todas as opções de cabo de uma vez
        $allCablePackages = $this->soollarApiRepository->getCablesByTypeAndColor($model, $color);

        if ($allCablePackages->isEmpty()) {
            SoollarImportHistory::finishProcess(SoollarImportHistory::STATUS_ERROR);
            throw new Exception("Nenhuma opção de cabo {$model} {$color} encontrada.");
        }

        $bestOption = null;

        foreach ($allCablePackages as $cablePackage) {
            $packageLength = (int) $cablePackage->size;
            if ($packageLength <= 0) {
                continue;
            }

            $requiredPackages = ceil($requiredLength / $packageLength);
            $totalCost = $requiredPackages * $cablePackage->price;

            // Se for a primeira opção ou a mais barata até agora
            if ($bestOption === null || $totalCost < $bestOption['cost']) {
                $bestOption = [
                    'description' => "{$requiredPackages}x Pacote de cabo de {$cablePackage->size} {$cablePackage->type} {$cablePackage->color}",
                    'cost' => $totalCost,
                    'quantity' => $requiredPackages,
                ];
            }
        }

        if ($bestOption === null) {
            SoollarImportHistory::finishProcess(SoollarImportHistory::STATUS_ERROR);
            throw new Exception("Não foi possível calcular o custo ideal para o cabo {$model} {$color}.");
        }

        return $bestOption;
    }
}
