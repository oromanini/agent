<?php

namespace Tests\Feature;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use App\Services\KitSearchService;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KitSearchServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->insertKitsToTest();
    }

    public function testKitSearchService_withValidParams_shouldReturnKits(): void
    {
        $service = new KitSearchService(
            kwp: 3,
            roof: RoofStructure::Colonial->value,
            tension: TensionPattern::mono220
        );

        dd($service->kitSearch());
    }

    private function insertKitsToTest(): void
    {
        Kit::create([
            'id' => 9843,
            'description' => 'Gerador edeltec solar sungrow 2,22 kwp mon. 220v s/estrutura (3k/555w)',
            'kwp' => 2.22,
            'cost' => 5123.49,
            'roof_structure' => 8,
            'tension_pattern' => 1,
            'components' => '["2 CONECTOR SC4 MACHO/FEMEA 1500V PROAUTO", "1 CABO EDELTEC SOLAR PV 1.8KVCC 6MM PRETO NBR 16612 - BOBINA 25MTS", "1 CABO EDELTEC SOLAR PV 1.8KVCC 6MM VERMELHO NBR 16612 - BOBINA 25MTS", "4 MODULO SOLAR SINE ENERGY 555W SN-555 144 HALF-CUT-CELLS MONO - 720 UN/CNTR", "1 INVERSOR 220V SUNGROW 1MPPT MONOFASICO 3KW SG3-0RS-S RS"]',
            'panel_specs' => '{"model":"SINE ENERGY 555W SN-555-HALF-CUT","logo":"\\/EdeltecApiPackage\\/img\\/panels\\/sine.png","efficiency":"21.48","warranty":12,"linear_warranty":25}',
            'inverter_specs' => '{"marca":"SUNGROW","model":"Dados T\\u00e9cnicos do Inve","logo":"\\/EdeltecApiPackage\\/img\\/inverters\\/sungrow.png","warranty":10}',
            'distributor_name' => 'EDELTEC',
            'distributor_code' => 'e6536ec1-b9b1-414c-974e-3b3b69b7230a',
            'availability' => '2023-09-05',
            'is_active' => 1,
            'created_at' => '2023-09-05 17:40:21',
            'updated_at' => '2023-09-05 17:40:21',
        ]);

        Kit::create([
            'id' => 123,
            'description' => 'Gerador edeltec solar sungrow 2,22 kwp mon. 220v s/estrutura (3k/555w)',
            'kwp' => 3.33,
            'cost' => 8123.49,
            'roof_structure' => 8,
            'tension_pattern' => 1,
            'components' => '["2 CONECTOR SC4 MACHO/FEMEA 1500V PROAUTO", "1 CABO EDELTEC SOLAR PV 1.8KVCC 6MM PRETO NBR 16612 - BOBINA 25MTS", "1 CABO EDELTEC SOLAR PV 1.8KVCC 6MM VERMELHO NBR 16612 - BOBINA 25MTS", "4 MODULO SOLAR SINE ENERGY 555W SN-555 144 HALF-CUT-CELLS MONO - 720 UN/CNTR", "1 INVERSOR 220V SUNGROW 1MPPT MONOFASICO 3KW SG3-0RS-S RS"]',
            'panel_specs' => '{"model":"SINE ENERGY 555W SN-555-HALF-CUT","logo":"\\/EdeltecApiPackage\\/img\\/panels\\/sine.png","efficiency":"21.48","warranty":12,"linear_warranty":25}',
            'inverter_specs' => '{"marca":"SUNGROW","model":"Dados T\\u00e9cnicos do Inve","logo":"\\/EdeltecApiPackage\\/img\\/inverters\\/sungrow.png","warranty":10}',
            'distributor_name' => 'EDELTEC',
            'distributor_code' => 'e6536ec1-b9b1-414c-974e-3b3b69b7230a',
            'availability' => '2023-09-05',
            'is_active' => 1,
            'created_at' => '2023-09-05 17:40:21',
            'updated_at' => '2023-09-05 17:40:21',
        ]);
    }
}
