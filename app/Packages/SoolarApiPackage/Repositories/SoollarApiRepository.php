<?php

namespace App\Packages\SoolarApiPackage\Repositories;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Models\Connector;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Models\Structure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SoollarApiRepository
{
    public function syncProducts(ProductCategoriesEnum $category, array $products): void
    {
        /** @var Model $model */
        $model = $this->getModelForCategory($category);

        if (!$model) {
            return;
        }

        DB::connection('soollar')->transaction(function () use ($model, $products) {
            foreach ($products as $productData) {
                if (empty($productData['name'])) {
                    continue;
                }

                $model->updateOrCreate(
                    ['name' => $productData['name']],
                    $productData
                );
            }
        });
    }

    private function getModelForCategory(ProductCategoriesEnum $category): ?Model
    {
        return match ($category) {
            ProductCategoriesEnum::MODULO => new Module(),
            ProductCategoriesEnum::INVERSOR => new Inverter(),
            ProductCategoriesEnum::ESTRUTURA => new Structure(),
            ProductCategoriesEnum::CABO => new Cable(),
            ProductCategoriesEnum::CONECTOR => new Connector(),
            default => null,
        };
    }
}
