<?php

namespace Tests\Feature\Gateways;

use App\Enums\RoofStructure;
use App\Models\Address;
use App\Models\PromotionalKit;
use App\Packages\EdeltecApiPackage\EdeltecApiService;
use App\Packages\EdeltecApiPackage\EdeltecGeneratorsRepository;
use App\Packages\EdeltecApiPackage\Enums\Category;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Services\PricingService;
use Generator;
use GuzzleHttp\Client;
use Tests\TestCase;

class EdeltecGatewayTest extends TestCase
{
    private EdeltecApiService $edeltecApiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->edeltecApiService = new EdeltecApiService(client: new Client());
    }

    public function testGateway_WithValidParameters_ShouldReturnKit(): void {

        $kit = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5
        );
        dd($kit);
        $this->assertNotEmpty($kit);
        $this->assertIsArray($kit);
    }
}
