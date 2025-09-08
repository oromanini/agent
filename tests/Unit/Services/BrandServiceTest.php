<?php

namespace Tests\Unit\Services;

use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandServiceTest extends TestCase
{
    use RefreshDatabase;

    private BrandService $brandService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->brandService = new BrandService();
        Storage::fake('public');
        Artisan::call('migrate:fresh', [
            '--database' => 'soollar',
            '--path' => 'database/migrations/soollar'
        ]);
    }

    public function testCreateBrandForInverterType(): void
    {
        $validatedData = [
            'brand' => 'TESTINVERTER1',
            'warranty' => 10,
            'overload' => 0.5,
        ];

        $newBrand = $this->brandService->createBrand($validatedData, 'inverter');

        $this->assertInstanceOf(Brand::class, $newBrand);
        $this->assertEquals('TESTINVERTER1', $newBrand->name);

        $this->assertDatabaseHas('soollar_test.inverter_brands', [
            'brand' => 'TESTINVERTER1',
            'warranty' => 10,
            'overload' => 0.5,
        ]);

        $this->assertDatabaseHas('agent_test.brands', [
            'name' => 'TESTINVERTER1',
            'type' => 'inverter',
        ]);
    }

    public function testCreateBrandForModuleType(): void
    {
        $validatedData = [
            'brand' => 'TESTMODEL1',
            'warranty' => 12,
            'linear_warranty' => 25,
        ];

        $newBrand = $this->brandService->createBrand($validatedData, 'panel');

        $this->assertInstanceOf(Brand::class, $newBrand);
        $this->assertEquals('TESTMODEL1', $newBrand->name);

        $this->assertDatabaseHas('soollar_test.module_brands', [
            "id"=> 1,
            "brand"=> "TESTMODEL1",
            "warranty"=> 12,
            "linear_warranty"=> 25,

        ]);

        $this->assertDatabaseHas('agent_test.brands', [
            'name' => 'TESTMODEL1',
            'type' => 'panel',
        ]);
    }

    public function test_correctly_increments_brand_enum_for_same_type()
    {
        Brand::create([
            'name' => 'MARCA A',
            'type' => 'panel',
            'brand_enum' => 5,
        ]);

        $validatedData = [
            'brand' => 'MARCA B',
            'warranty' => 12,
            'linear_warranty' => 25,
        ];

        $this->brandService->createBrand($validatedData, 'panel');

        $this->assertDatabaseHas('agent_test.brands', [
            'name' => 'MARCA B',
            'type' => 'panel',
            'brand_enum' => 6,
        ]);
    }
}
