<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\InstallationCost;
use Tests\TestCase;

class InstallationCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $panelQuantity = 10;
        $this->installationCost = new InstallationCost($panelQuantity, false);
        $this->costs = [
            'panel_price' => 120,
            'displacement' => 10,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::INSTALLATION)
            ->withChangeHistory()
            ->build();
    }

    public function testInstalationCost(): void
    {
        $cost = $this->installationCost->cost();

        $this->assertEquals(1200, $cost);
    }
}
