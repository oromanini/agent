<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\DeliveryCost;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\InstallationCost;
use Tests\TestCase;

class DeliveryCostTest extends TestCase
{
    public function testDeliveryCost_WithEnabled(): void
    {
        $costs = [
            'estimated_delivery_percentage' => 0.025,
            'enabled' => 1
        ];
        (new WorkCostBuilder())
            ->withCosts($costs)
            ->withClassification(WorkCostClassificationEnum::DELIVERY)
            ->withChangeHistory()
            ->build();

        $directCurrentCost = new DeliveryCost(100000);
        $this->assertEquals(2500, $directCurrentCost->cost());
    }

    public function testDeliveryCost_WithDisabled(): void
    {
        $costs = [
            'estimated_delivery_percentage' => 0.025,
            'enabled' => 0
        ];
        (new WorkCostBuilder())
            ->withCosts($costs)
            ->withClassification(WorkCostClassificationEnum::DELIVERY)
            ->withChangeHistory()
            ->build();

        $directCurrentCost = new DeliveryCost(100000);
        $this->assertEquals(0, $directCurrentCost->cost());
    }
}
