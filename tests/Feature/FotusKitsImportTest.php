<?php

namespace Feature;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use App\Services\Fotus\FotusKitsImportService;
use App\Services\KitSearchService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FotusKitsImportTest extends TestCase
{
    private FotusKitsImportService $fotusKitsImportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fotusKitsImportService = new FotusKitsImportService('tests/Feature/files/fotus/kits.csv');
    }

    public function testFotusKitsImportService(): void
    {
        $this->fotusKitsImportService->importStringMonoInverterKits();
        $totalKitsCount = (count(RoofStructure::cases()) - 1) * 3;
        $this->assertCount($totalKitsCount, Kit::all());
    }

//    public function testService()
//    {
//        DB::insert(
//            "INSERT INTO agent_test.active_kits (id, panel_brand, inverter_brand, distributor, is_active, last_updated_time) VALUES (120, 'SUNOVA', 'SOLPLANET', 'FOTUS', 1, '2024-11-24 23:44:55');"
//        );
//
//        $kits = (new KitSearchService(
//            4.44, RoofStructure::COLONIAL->name,
//            TensionPattern::MONOFASICO_220V->value
//        ))->kitSearch();
//
//        dump($kits);
//    }
}
