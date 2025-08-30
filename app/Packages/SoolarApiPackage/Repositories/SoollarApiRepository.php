<?php

namespace App\Packages\SoolarApiPackage\Repositories;

use App\Models\Kit;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\KitsManager;
use App\Packages\SoolarApiPackage\Models\Cable;
use App\Packages\SoolarApiPackage\Models\Connector;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Models\Structure;
use App\Packages\SoolarApiPackage\Services\CableService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SoollarApiRepository
{
    const INVERTER_TYPE = 'inverter';
    private int $createdProductsCount;
    private int $updatedProductsCount;

    public function __construct()
    {
        $this->createdProductsCount = 0;
        $this->updatedProductsCount = 0;
    }

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

                $this->updateOrCreate(
                    model: $model,
                    searchAttribute: ['name' => $productData['name']],
                    data: $productData
                );
            }
        });

        SoollarImportHistory::updateProcess(
            created_products: $this->createdProductsCount,
            updated_products: $this->updatedProductsCount,
        );
    }

    public function syncKits(): void
    {
        (new KitsManager(
            $this, new CableService($this)
        ))->handle();
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
    public function getKitByDescription(string $description): ?Kit
    {
        return Kit::query()
            ->where('description', $description)
            ->where('distributor_name', 'SOOLLAR')
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getCable(int $moduleQuantity, string $type, string $color): Collection
    {
        return Cable::query()
            ->where('type', strtoupper($color))
            ->where('model', $type)
            ->get();
    }

    /**
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function getCableByLength(string $type, string $color, int $length): ?Cable
    {
        return Cable::query()
            ->where('model', strtoupper($type))
            ->where('type', strtoupper($color))
            ->where('size', 'LIKE', '%' . $length . 'MT%')
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getConnectors(): Connector
    {
        return Connector::query()->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getStructureByModelName(string $modelName): ?Structure
    {
        return Structure::query()
            ->where('model', 'LIKE', '%' . $modelName . '%')
            ->first();
    }

    public function deactivateAllKits(): void
    {
        Kit::query()
            ->where('distributor_name', 'SOOLLAR')
            ->update(['is_active' => false]);
    }

    public function getRecentKits(int $limit): Collection
    {
        return Kit::query()
            ->where('is_active', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function countActiveKits(): int
    {
        return Kit::query()
            ->where('is_active', true)
            ->count();
    }

    private function updateOrCreate(
        Model $model,
        array $searchAttribute,
        array $data
    ):void {
        $object = $model->query()->where($searchAttribute);

        if ($object->exists()) {
            $object->first()->update($data);
            $this->updatedProductsCount++;

            return;
        }
        $model->create($data);
        $this->createdProductsCount++;
    }
}
