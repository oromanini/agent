<?php

namespace Feature\Pricing;

use App\Enums\RoofStructure;
use App\Models\Address;
use App\Models\PromotionalKit;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Services\PricingService;
use Generator;
use Tests\TestCase;

class PricingTest extends TestCase
{
    public function testPricingService_WithNotPromotionalKits_ShouldReturnPrice(): void
    {
        $pricingService = new PricingService();

        $pricing = $pricingService->calculateFinalPrice([
            'kwp' => 4.44,
            'roof_structure' => RoofStructure::Colonial,
            'cost' => 7000,
            'panel_count' => 8,
            'address_id' => (Address::factory()->create())->id,
            'panelBrand' => PanelBrand::SINE->value,
            'panelPower' => 555,
            'inverterBrand' => InverterBrand::SAJ->value,
        ]);

        $this->assertEquals(
            expected: 13450,
            actual: $pricing['finalPrice']
        );
    }
}
