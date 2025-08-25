<?php

namespace App\Packages\SoolarApiPackage\tests;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\KitsManager;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use App\Packages\SoolarApiPackage\SoollarApiManager;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KitsManagerTest extends TestCase
{
    private SoollarApiRepository $soollarApiRepository;
    private KitsManager $kitsManager;

    public function setUp(): void
    {
        parent::setUp();
        DB::connection('soollar')->table('inverter_brands')->truncate();
        DB::connection('soollar')->table('module_brands')->truncate();

        //      1. Create brands
        InverterBrand::factory()->create([
            'brand' => 'SAJ',
            'active' => true,
        ]);
        ModuleBrand::factory()->create([
            'brand' => 'RENEPV',
            'active' => true,
        ]);

        //      2. Get Products
        $this->soollarApiRepository = new SoollarApiRepository();
        $this->kitsManager = new KitsManager(
            repository: $this->soollarApiRepository,
            cableService: new CableService($this->soollarApiRepository),
        );

        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                (new SoollarApiManager($this->soollarApiRepository))->handle($category, $warehouse);
            }
        }
    }


    public function testKitsManagerHandle(): void
    {
        $response = json_decode($this->kitsManager->handle()->content(), true);
        self::assertTrue($response['total'] > 0);
    }
}
