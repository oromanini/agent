<?php

namespace App\Packages\SoolarApiPackage\Services;

use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use Exception;

class CableService
{

    public function __construct(private readonly SoollarApiRepository $soollarApiRepository)
    {}

    public function getBestCableOption(int $moduleQuantity, string $color): Cable
    {
        $requiredLength = $moduleQuantity * 2.5;

        $model = $moduleQuantity > 30 ? '6MM' : '4MM';

        $candidateCables = $this->soollarApiRepository->getCable(
            moduleQuantity: $moduleQuantity,
            type: $model,
            color: $color
        );

        $suitableCables = $candidateCables->filter(function ($cable) use ($requiredLength) {
            $sizeInt = (int) filter_var($cable->size, FILTER_SANITIZE_NUMBER_INT);
            return $sizeInt >= $requiredLength;
        });

        if ($suitableCables->isEmpty()) {
            throw new Exception("Nenhum pacote de cabo de {$model} {$color} encontrado para a metragem necessária de {$requiredLength}m.");
        }

        $bestOption = $suitableCables->sortBy(function ($cable) {
            return (int) filter_var($cable->size, FILTER_SANITIZE_NUMBER_INT);
        })->first();

        return $bestOption;
    }
}

