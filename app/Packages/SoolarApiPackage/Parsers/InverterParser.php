<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use App\Packages\SoolarApiPackage\Enums\CommonInverterBrandsEnum;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoollarApiManager;

class InverterParser
{
    public function parseInverterProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);

        if (stripos($cleanName, 'garantia total para inversores') !== false) {
            $model = null;
            if (preg_match('/(\d+\s*anos)/i', $cleanName, $durationMatch)) {
                $model = $durationMatch[1];
            }
            return [
                'type' => 'warranty',
                'name' => $cleanName,
                'model' => $model,
                'brand' => null,
                'price' => SoollarApiManager::cleanPrice($rawPrice),
                'power' => null,
                'voltage' => null,
                'stock' => 'pronta entrega',
                'distribution_center' => $warehouse->value,
                'category' => $category->value,
            ];
        }

        $stock = SoollarApiManager::getDeliveryStock($originalName);

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $cleanName));

        $voltage = '220V';
        if (preg_match('/(\d{3,4}v)/i', $nameWithoutStock, $voltageMatch)) {
            $voltage = strtoupper($voltageMatch[1]);
        }

        $power = null;
        if (preg_match('/([\d\.\,]+)\s*k/i', $nameWithoutStock, $powerMatch)) {
            $power = (float) str_replace(',', '.', $powerMatch[1]);
        }

        $knownBrands = array_map('strtolower', array_column(CommonInverterBrandsEnum::cases(), 'value'));
        $brand = null;
        foreach ($knownBrands as $knownBrand) {
            if (str_contains($nameWithoutStock, $knownBrand)) {
                $brand = $knownBrand;
                break;
            }
        }

        $modelName = $nameWithoutStock;
        if ($brand) {
            $modelName = str_ireplace($brand, '', $modelName);
        }
        if ($power) {
            $modelName = str_ireplace((string)$power . 'k', '', $modelName);
        }
        if ($voltage) {
            $modelName = str_ireplace($voltage, '', $modelName);
        }

        $model = trim(preg_replace('/^(micro-inversor|micro inversor|inversor|inv|garantia total|em estoque|no stock|\s*-\s*.*$)/i', '', $modelName));
        $model = trim(preg_replace('/\s+/', ' ', $model));

        return [
            'type' => 'inverter',
            'name' => $cleanName,
            'model' => $model,
            'brand' => $brand ? strtoupper($brand) : null,
            'price' => SoollarApiManager::cleanPrice($rawPrice),
            'power' => $power,
            'voltage' => $voltage,
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

}
