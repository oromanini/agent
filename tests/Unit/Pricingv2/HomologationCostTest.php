<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\HomologationCost;
use Tests\TestCase;

class HomologationCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $costs = ['homologation_cost_range' =>
            [
                5 => 100,
                10 => 200,
                15 => 300
            ]
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($costs)
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::HOMOLOGATION)
            ->build();
    }

    public function testHomologationCost_With12kWP_ShouldReturn200(): void
    {
        $cost = (new HomologationCost(3))->cost();
        $cost2 = (new HomologationCost(7))->cost();
        $cost3 = (new HomologationCost(13))->cost();
        $cost4 = (new HomologationCost(40))->cost();

        $this->assertEquals(100, $cost);
        $this->assertEquals(200, $cost2);
        $this->assertEquals(300, $cost3);
        $this->assertEquals(2000, $cost4);
    }
}
