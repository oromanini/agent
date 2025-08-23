<?php

namespace App\Packages\SoolarApiPackage\Repositories;

use App\Models\Kit;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Models\Connector;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use App\Packages\SoolarApiPackage\Models\Structure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SoollarApiRepository
{
    const INVERTER_TYPE = 'inverter';

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

    public function getModuleBrands(): Collection|array
    {
        return ModuleBrand::query()
            ->where('active', true)
            ->get();
    }

    public function getInverterBrands(): Collection|array
    {
        return InverterBrand::class::query()
            ->where('active', true)
            ->get();
    }

    public function findModulesByBrand(ModuleBrand $moduleBand): Collection|array
    {
        return Module::query()
            ->where('brand', $moduleBand->brand)
            ->get();
    }

    public function findInvertersByBrand(InverterBrand $inverterBand): Collection|array
    {
        return Inverter::query()
            ->where('brand', $inverterBand->brand)
            ->where('type', self::INVERTER_TYPE)
            ->get();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getKitByDescription(string $description): Kit
    {
        return Kit::query()
            ->where('description', $description)
            ->where('distributor_name', 'SOOLLAR')
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getCable(int $moduleQuantity, string $type, string $color): Cable
    {
        return Cable::query()
            ->where('type', strtoupper($color))
            ->where('model', $type)
            ->get();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getConnectors(): Connector
    {
        return Connector::query()->first();
    }
}
