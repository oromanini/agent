<?php

namespace Feature\Pricing;

use App\Services\PricingService;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    public function testFinalValueMethod_WithValidParams_shouldReturnFinalValue(): void
    {
        $this->seed(DatabaseSeeder::class);

        $finalValue = (new PricingService())->calculateFinalPrice([
            'cost' => 10000,
            'kwp' => 5,
            'panel_count' => 10,
            'finalValue' => 13000,
            'client' => 1,
            'roof_structure' => 1
        ]);

        $this->assertEquals(17250, $finalValue['finalPrice']);
    }

    public function testPricing(): void
    {

    }
}
