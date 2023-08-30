<?php

namespace Tests\Feature\KitSearch;

use App\Enums\RoofStructure;
use App\Models\ActiveKit;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Services\KitSearchService;
use Tests\TestCase;

class KitSearchTest extends TestCase
{


    public function testKitSearch_WithValidParams_ShouldReturnKits(): void
    {
        $kwp = 4;
        $roof = RoofStructure::matchRoof(StructureType::COLONIAL)->value;
        $tension = 'MONOFÁSICO-220v';

        $distributor = 'EDELTEC';

        ActiveKit::factory()->create([
            'panel_brand' => 'HONOR',
            'inverter_brand' => 'SAJ',
            'distributor' => $distributor
        ]);

        ActiveKit::factory()->create([
            'panel_brand' => 'HONOR',
            'inverter_brand' => 'SUNGROW',
            'distributor' => $distributor
        ]);

        ActiveKit::factory()->create([
            'panel_brand' => 'HONOR',
            'inverter_brand' => 'DEYE',
            'distributor' => $distributor
        ]);

        $kits = (new KitSearchService())->kitSearch($kwp, $roof, $tension);

        $this->assertCount(3, $kits);
    }
}
