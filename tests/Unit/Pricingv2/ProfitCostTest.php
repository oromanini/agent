<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\DeliveryCost;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\InstallationCost;
use App\Models\Pricing\ProfitCost;
use App\Models\WorkCost;
use Tests\TestCase;

class ProfitCostTest extends TestCase
{
    public function testProfitCost_WithEnabled(): void
    {
        $costs = [
            'profit' => 0.12,
            'enabled' => 1
        ];
        (new WorkCostBuilder())
            ->withCosts($costs)
            ->withClassification(WorkCostClassificationEnum::PROFIT)
            ->withChangeHistory()
            ->build();
        $directCurrentCost = new ProfitCost(10000);
        $this->assertEquals(1200, $directCurrentCost->cost());
    }
}
