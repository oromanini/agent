<?php

namespace Tests\Feature\Packages\SoolarApiPackage;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SoolarProductsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SoollarApiRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate', [
            '--database' => 'soollar',
            '--path' => 'database/migrations/soollar',
        ]);

        $this->repository = new SoollarApiRepository();
    }

    public function test_sync_products_creates_new_record_if_not_exists()
    {
        $mockModuleData = [
            [
                'name' => 'modulo 580w nplus bifacial 30mm',
                'power' => '580W',
                'brand' => 'NPLUS',
                'model' => 'BIFACIAL 30MM',
                'price' => 450.0,
                'distribution_center' => 'CDTESTE',
                'category' => 'Modulo',
            ]
        ];

        $this->repository->syncProducts(ProductCategoriesEnum::MODULO, $mockModuleData);

        $this->assertDatabaseHas(
            table: 'modules',
            data: [
                'name' => 'modulo 580w nplus bifacial 30mm',
                'price' => 450.0
            ],
            connection: 'soollar');

        $this->assertDatabaseCount(
            table: 'modules',
            count: 1,
            connection: 'soollar'
        );
    }

    public function test_sync_products_updates_existing_record()
    {
        $initialModuleData = [
            [
                'name' => 'modulo 580w nplus bifacial 30mm',
                'power' => '580W',
                'brand' => 'NPLUS',
                'model' => 'BIFACIAL 30MM',
                'price' => 450.0,
                'distribution_center' => 'CDTESTE',
                'category' => 'Modulo',
            ]
        ];
        $this->repository->syncProducts(ProductCategoriesEnum::MODULO, $initialModuleData);

        $this->assertDatabaseHas(
            table: 'modules',
            data: ['price' => 450.0],
            connection: 'soollar'
        );

        $updatedModuleData = [
            [
                'name' => 'modulo 580w nplus bifacial 30mm',
                'power' => '580W',
                'brand' => 'NPLUS',
                'model' => 'BIFACIAL 30MM',
                'price' => 425.50,
                'distribution_center' => 'CDTESTE',
                'category' => 'Modulo',
            ]
        ];

        $this->repository->syncProducts(ProductCategoriesEnum::MODULO, $updatedModuleData);

        $this->assertDatabaseHas(
            table: 'modules',
            data: [
                'name' => 'modulo 580w nplus bifacial 30mm',
                'price' => 425.50
            ],
            connection: 'soollar'
        );

        $this->assertDatabaseCount(
            table: 'modules',
            count: 1, connection: 'soollar'
        );
    }
}
