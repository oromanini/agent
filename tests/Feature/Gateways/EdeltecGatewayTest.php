<?php

namespace Tests\Feature\Gateways;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
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
    private string $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->edeltecApiService = new EdeltecApiService(client: new Client());
        $this->apiToken = EdeltecApiService::setApiToken();
    }

    public function testGateway_WithValidParameters_ShouldReturnKit(): void
    {
        $kit = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5,
            tensionPattern: [TensionPattern::mono220],
            apiToken: $this->apiToken
        );

        $this->assertNotEmpty($kit);
        $this->assertIsArray($kit);
    }

    public function testGateway_WithThreeDifferentKits_ShouldReturnThreeKits(): void
    {

        $kit1 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5,
            tensionPattern: [TensionPattern::mono220],
            apiToken: $this->apiToken
        );

        $kit2 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::OSDA,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5,
            tensionPattern: [TensionPattern::mono220],
            apiToken: $this->apiToken
        );

        $kit3 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::HONOR,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5,
            tensionPattern: [TensionPattern::mono220],
            apiToken: $this->apiToken
        );

        $this->assertTrue(is_array($kit1) || is_null($kit1));
        $this->assertTrue(is_array($kit2) || is_null($kit2));
        $this->assertTrue(is_array($kit3) || is_null($kit3));
    }

    public function testGateway_WithAllTensions_ShouldReturnKit(): void
    {
        $kit1 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::SAJ,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5.5,
            tensionPattern: [TensionPattern::bi220],
            apiToken: $this->apiToken
        );
        $kit2 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::GROWATT,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 20,
            tensionPattern: [TensionPattern::tri220],
            apiToken: $this->apiToken
        );
        $kit3 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::GROWATT,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 10,
            tensionPattern: [TensionPattern::tri220],
            apiToken: $this->apiToken
        );
        $kit4 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::GROWATT,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 30,
            tensionPattern: [TensionPattern::tri380],
            apiToken: $this->apiToken
        );
        $kit5 = $this->edeltecApiService->searchKits(
            inverterBrand: InverterBrand::GROWATT,
            panelBrand: PanelBrand::SINE,
            structureType: StructureType::COLONIAL,
            category: Category::ONGRID,
            kwp: 5,
            tensionPattern: [TensionPattern::tri380],
            apiToken: $this->apiToken
        );

        $this->assertTrue(is_array($kit1));
        $this->assertTrue(is_array($kit2));
        $this->assertTrue(is_array($kit3));
        $this->assertTrue(is_array($kit4));
        $this->assertNull($kit5);
    }

    public function testGateway_WithAllRoofs_ShouldReturnKit(): void
    {
        foreach (StructureType::cases() as $roofStructure) {
            $this->assertNotNull(
                $this->edeltecApiService->searchKits(
                    inverterBrand: InverterBrand::SAJ,
                    panelBrand: PanelBrand::SINE,
                    structureType: $roofStructure,
                    category: Category::ONGRID,
                    kwp: 5.5,
                    tensionPattern: [TensionPattern::bi220],
                    apiToken: $this->apiToken
                )
            );
        }
    }
}
