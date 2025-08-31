<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoollarApiManager;

class ConnectorParser
{
    public function parseConnectorProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = trim(preg_replace('/^(conector)\s*/i', '', $originalName));

        return [
            'name' => strtolower($cleanName),
            'price' => SoollarApiManager::cleanPrice($rawPrice),
            'stock' => SoollarApiManager::getDeliveryStock($originalName),
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }
}
