<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoollarApiManager;

class CableParser
{
    public function parseCableProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);
        $model = null;
        $size = null;
        $type = null;

        if (preg_match('/(\d+mm)/', $cleanName, $modelMatch)) {
            $model = $modelMatch[1];
        }

        if (preg_match('/(\d+mt)/', $cleanName, $sizeMatch)) {
            $size = $sizeMatch[1];
        }

        if (str_contains($cleanName, 'preto')) {
            $type = 'PRETO';
        } elseif (str_contains($cleanName, 'vermelho')) {
            $type = 'VERMELHO';
        }

        $stock = SoollarApiManager::getDeliveryStock($originalName);

        return [
            'name' => strtolower($originalName),
            'model' => $model,
            'size' => $size,
            'type' => $type,
            'price' => SoollarApiManager::cleanPrice($rawPrice),
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }
}
