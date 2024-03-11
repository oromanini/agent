<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use Tests\TestCase;

class RoyaltyCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->costs = ['estimated_percentage' => 0.02];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::ROYALTY)
            ->withChangeHistory()
            ->build();
    }

    public function testInstallationCost(): void
    {
        $directCurrentCost = new RoyaltyCost(10000);
        $this->assertEquals(200, $directCurrentCost->cost());
    }
}
