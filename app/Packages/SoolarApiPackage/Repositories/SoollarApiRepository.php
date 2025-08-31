<?php

namespace App\Packages\SoolarApiPackage\Repositories;

use App\Models\Kit;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
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
use Illuminate\Support\Facades\Storage;

class SoollarApiRepository
{
    const INVERTER_TYPE = 'inverter';
    private int $createdProductsCount = 0;
    private int $updatedProductsCount = 0;

    public function syncProducts(ProductCategoriesEnum $category, WarehouseEnum $warehouse, array $products): void
    {
        $model = $this->getModelForCategory($category);
        if (!$model) {
            return;
        }

        $logFile = 'soollar_debug_products.txt';
        $header = "\n--- Log de Batch: " . now()->format('d/m/Y H:i:s') . " | Categoria: {$category->value} | Armazém: {$warehouse->value} ---\n";
        Storage::append($logFile, $header);

        DB::connection('soollar')->transaction(function () use ($model, $products, $logFile) {
            foreach ($products as $productData) {
                if (empty($productData['name'])) {
                    continue;
                }

                $status = $this->updateOrCreate(
                    model: $model,
                    searchAttribute: ['name' => $productData['name']],
                    data: $productData
                );

                $price = $productData['price'] ?? 'N/A';
                $logLine = "[$status] - Nome: {$productData['name']} | Preço: {$price}";
                Storage::append($logFile, $logLine);
            }

            //atualiza a cada 10 produtos
            ($this->createdProductsCount + $this->updatedProductsCount) % 10 == 0
            && SoollarImportHistory::updateProcess(
                createdProducts: $this->createdProductsCount,
                updatedProducts: $this->updatedProductsCount
            );
        });
    }

    private function updateOrCreate(Model $model, array $searchAttribute, array $data): string
    {
        $object = $model->query()->where($searchAttribute)->first();

        if ($object) {
            $object->update($data);
            $this->updatedProductsCount++;
            return 'Atualizado';
        }

        $model->create($data);
        $this->createdProductsCount++;
        return 'Criado';
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

    public function getKitByDescription(string $description): ?Kit
    {
        return Kit::query()
            ->where('description', $description)
            ->where('distributor_name', 'SOOLLAR')
            ->first();
    }

    public function getCable(int $moduleQuantity, string $type, string $color): Collection
    {
        return Cable::query()
            ->where('type', strtoupper($color))
            ->where('model', $type)
            ->get();
    }

    public function getCableByLength(string $type, string $color, int $length): ?Cable
    {
        return Cable::query()
            ->where('model', strtoupper($type))
            ->where('type', strtoupper($color))
            ->where('size', 'LIKE', '%' . $length . 'MT%')
            ->first();
    }

    public function getConnectors(): Connector
    {
        return Connector::query()->first();
    }

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
}
