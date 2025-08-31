<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoollarApiManager;

class StructureParser
{
    public function parseStructureProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): ?array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];

        if (stripos($originalName, 'kit') === false) {
            return null;
        }

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $originalName));
        $model = trim(preg_replace('/kit fixação/i', '', $nameWithoutStock));

        return [
            'name' => strtolower($originalName),
            'model' => strtolower(trim($model)),
            'price' => SoollarApiManager::cleanPrice($rawPrice),
            'stock' => 'unknown',
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }
}
