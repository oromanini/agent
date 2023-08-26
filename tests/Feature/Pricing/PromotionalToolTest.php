<?php

namespace Tests\Feature\Pricing;

use App\Enums\RoofStructure;
use App\Models\Address;
use App\Models\PromotionalKit;
use App\Services\PricingService;
use Generator;
use Tests\TestCase;

class PromotionalToolTest extends TestCase
{
    protected PromotionalKit $promotionalKit;


    function promotionalScenarios(): Generator
    {
        yield 'is promotional scenario' => [
            'kwp' => 3.2,
            'roof_structure' => RoofStructure::Colonial->value,
            'cost' => 10000,
            'panel_count' => 5,
            'panelBrand' => 'ja',
            'panelPower' => 550,
            'inverterBrand' => 'growatt',
            'isPromotional' => true,
        ];

        yield 'is default scenario' => [
            'kwp' => 4.4,
            'roof_structure' => RoofStructure::Colonial->value,
            'cost' => 15000,
            'panel_count' => 5,
            'panelBrand' => 'jinko',
            'panelPower' => 550,
            'inverterBrand' => 'growatt',
            'isPromotional' => false,
        ];
    }

    /**
     * @dataProvider  promotionalScenarios
     */
    public function testPricingServiceFindOrFailPromotionalKits_WithPromotionalKits_ShouldReturnTwoKitUuids(
        float  $kwp,
        int    $roof_structure,
        float  $cost,
        int    $panel_count,
        string $panelBrand,
        float  $panelPower,
        string $inverterBrand,
        bool   $isPromotional
    ): void {

        $pricingService = new PricingService();

        PromotionalKit::updateOrCreate(
            [
                'panel_brand' => 'ja',
                'panel_power' => 550,
                'inverter_brand' => 'growatt',
                'kwp' => 3.2,
            ],
            [
                'final_value' => 16000,
            ]
        );

        $pricing = $pricingService->calculateFinalPrice([
            'kwp' => $kwp,
            'roof_structure' => $roof_structure,
            'cost' => $cost,
            'panel_count' => $panel_count,
            'address_id' => (Address::factory()->create())->id,
            'panelBrand' => $panelBrand,
            'panelPower' => $panelPower,
            'inverterBrand' => $inverterBrand,
        ]);

        $this->assertEquals(
            expected: $isPromotional,
            actual: $pricing['isPromotional']
        );
    }
}
