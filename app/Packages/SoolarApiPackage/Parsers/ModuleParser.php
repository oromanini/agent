<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use App\Packages\SoolarApiPackage\Enums\CommonModuleBrandsEnum;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoollarApiManager;

class ModuleParser
{
    public function parseModuleProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);

        $power = null;
        $brand = null;
        $model = null;

        $knownBrands = array_map('strtolower', array_column(CommonModuleBrandsEnum::cases(), 'value'));

        if (preg_match('/(\d+)\s*w/i', $cleanName, $powerMatch)) {
            $power = (int)$powerMatch[1];
            $remainingName = trim(str_ireplace($powerMatch[0], '', $cleanName));

            foreach ($knownBrands as $knownBrand) {
                if (str_contains($remainingName, $knownBrand)) {
                    $brand = $knownBrand;
                    $remainingName = trim(str_ireplace($knownBrand, '', $remainingName));
                    break;
                }
            }

            $model = trim(preg_replace('/^(r\$|previsão de entrega|previsão de chegada|e|pronta entrega).*?-?\s*/i', '', $remainingName));
            $model = trim(preg_replace('/\s+/', ' ', $model));
        }

        return [
            'name' => $originalName,
            'power' => $power,
            'brand' => strtoupper($brand ?? ''),
            'model' => strtolower($model ?? ''),
            'price' => SoollarApiManager::cleanPrice($rawPrice),
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }
}
