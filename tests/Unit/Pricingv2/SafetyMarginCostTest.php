<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\SafetyMarginCost;
use Tests\TestCase;

class SafetyMarginCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->costs = ['estimated_percentage' => 0.015];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::SAFETY_MARGIN)
            ->withChangeHistory()
            ->build();
    }

    public function testInstallationCost(): void
    {
        $directCurrentCost = new SafetyMarginCost(10000);
        $this->assertEquals(150, $directCurrentCost->cost());
    }
}
