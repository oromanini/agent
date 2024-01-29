<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\WorkMonitoringCost;
use Tests\TestCase;

class WorkMonitoringCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $costs = ['monitoring_cost_range' =>
            [
                5 => 160,
                10 => 230,
                15 => 450,
                20 => 620,
            ]
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($costs)
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::WORK_MONITORING)
            ->build();
    }

    public function testWorkMonitoringCost_WithDifferentKWPs_ShouldAssert(): void
    {
        $cost = (new WorkMonitoringCost(3))->cost();
        $cost2 = (new WorkMonitoringCost(7))->cost();
        $cost3 = (new WorkMonitoringCost(13))->cost();
        $cost4 = (new WorkMonitoringCost(18))->cost();
        $cost5 = (new WorkMonitoringCost(30))->cost();

        $this->assertEquals(160, $cost);
        $this->assertEquals(230, $cost2);
        $this->assertEquals(450, $cost3);
        $this->assertEquals(620, $cost4);
        $this->assertEquals(900, $cost5);
    }
}
