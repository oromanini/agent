<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\InstallationCost;
use Tests\TestCase;

class DirectCurrentCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->costs = ['estimated_material_percentage' => 0.04];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::DIRECT_CURRENT_MATERIAL)
            ->withChangeHistory()
            ->build();
    }

    public function testInstallationCost(): void
    {
        $directCurrentCost = new DirectCurrentCost(10000);
        $this->assertEquals(800, $directCurrentCost->cost());

        $directCurrentCost = new DirectCurrentCost(100000);
        $this->assertEquals(4000, $directCurrentCost->cost());
    }
}
