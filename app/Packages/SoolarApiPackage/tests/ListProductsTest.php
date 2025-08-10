<?php

namespace App\Packages\SoolarApiPackage\tests;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\SoolarApiService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ListProductsTest extends TestCase
{
    private SoolarApiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SoolarApiService();
    }

    /**
     * @dataProvider productCategoryProvider
     */
    public function testListProductsByCategory(ProductCategoriesEnum $category, array $expectedKeys): void
    {
        $response = $this->service->listProducts($category);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
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
                ['name', 'power', 'brand', 'model', 'price']
            ],
            'inverters' => [
                ProductCategoriesEnum::INVERSOR,
                ['type', 'name', 'model', 'brand', 'price', 'voltage', 'stock']
            ],
        ];
    }

    // --- Temporary Debug Tests ---

    public function testDebugModules(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->listProducts(ProductCategoriesEnum::MODULO, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugInverters(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->listProducts(ProductCategoriesEnum::INVERSOR, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugConnectors(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->listProducts(ProductCategoriesEnum::CONECTOR, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugStructures(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->listProducts(ProductCategoriesEnum::ESTRUTURA, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }

    public function testDebugCables(): void
    {
        $this->markTestSkipped("Uncomment dd to proceed and comment this.");
//        dd($this->service->listProducts(ProductCategoriesEnum::CABO, warehouse: WarehouseEnum::FEIRA_DE_SANTANA_BA));
    }
}
