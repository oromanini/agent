<?php

namespace App\Packages\SoolarApiPackage\Services;

use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use Exception;

class CableService
{

    public function __construct(private readonly SoollarApiRepository $soollarApiRepository)
    {
    }

    public function getBestCableOption(int $moduleQuantity, string $color): array
    {
        $requiredLength = $moduleQuantity * 2.5;

        $model = $moduleQuantity > 30 ? '6MM' : '4MM';

        // 1. Busque o pacote de cabo de 25 metros
        $cablePackage = $this->soollarApiRepository->getCableByLength(
            type: $model,
            color: $color,
            length: 25 // Procurando especificamente o pacote de 25 metros
        );

        // Se o pacote de 25 metros não for encontrado, lance a exceção
        if (!$cablePackage) {
            throw new Exception("Pacote de cabo {$model} {$color} de 25m não encontrado.");
        }

        // 2. Calcule a quantidade de pacotes necessária
        $requiredPackages = ceil($requiredLength / 25);

        // 3. Calcule o custo total
        $totalCost = $requiredPackages * $cablePackage->price;

        return [
            'description' => "{$requiredPackages}x Pacote de cabo de {$cablePackage->size} {$cablePackage->type} {$cablePackage->color}",
            'cost' => $totalCost,
            'quantity' => $requiredPackages,
        ];
    }
}
