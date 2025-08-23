<?php

namespace App\Packages\SoolarApiPackage\tests;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoolarApiManager;
use Tests\TestCase;

class ListProductsTest extends TestCase
{
    private SoolarApiManager $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SoolarApiManager();
    }

    /**
     * @dataProvider productCategoryProvider
     */
    public function testListProductsByCategory(ProductCategoriesEnum $category, array $expectedKeys): void
    {
        $data = $this->service->handle($category, WarehouseEnum::FEIRA_DE_SANTANA_BA);

        $this->assertArrayNotHasKey('error', $data, "The API service returned an error: " . ($data['error'] ?? 'Unknown error'));

        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('products', $data);
        $this->assertIsArray($data['products']);

        if ($data['total'] > 0) {
            $this->assertNotEmpty($data['products']);

            $firstProduct = $data['products'][0];

            foreach ($expectedKeys as $key) {
                $this->assertArrayHasKey($key, $firstProduct, "Key '{$key}' is missing for category {$category->value}");
            }
        } else {
            $this->markTestSkipped("No products returned for category {$category->value}, cannot verify structure.");
        }
    }

    public static function productCategoryProvider(): array
    {
        return [
            'modules' => [
                ProductCategoriesEnum::MODULO,
                ['name', 'power', 'brand', 'model', 'price', 'distribution_center', 'category']
            ],
            'inverters' => [
                ProductCategoriesEnum::INVERSOR,
                ['type', 'name', 'model', 'brand', 'price', 'voltage', 'stock', 'distribution_center', 'category']
            ],
            'connectors' => [
                ProductCategoriesEnum::CONECTOR,
                ['name', 'price', 'stock', 'distribution_center', 'category']
            ],
            'structures' => [
                ProductCategoriesEnum::ESTRUTURA,
                ['name', 'model', 'price', 'stock', 'distribution_center', 'category']
            ],
            'cables' => [
                ProductCategoriesEnum::CABO,
                ['name', 'model', 'size', 'type', 'price', 'stock', 'distribution_center', 'category']
            ],
        ];
    }

    public function testDebugModules(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->handle(ProductCategoriesEnum::MODULO, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugInverters(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->handle(ProductCategoriesEnum::INVERSOR, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugConnectors(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->handle(ProductCategoriesEnum::CONECTOR, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugStructures(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->handle(ProductCategoriesEnum::ESTRUTURA, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugCables(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->handle(ProductCategoriesEnum::CABO, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }
}
