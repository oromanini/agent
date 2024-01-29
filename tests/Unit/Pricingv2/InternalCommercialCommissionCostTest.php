<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Pricing\InternalCommercialCommissionCost;
use Tests\TestCase;

class InternalCommercialCommissionCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->costs = [
            'commission_percentage' => 0.01,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::INTERNAL_COMMERCIAL_COMMISSION)
            ->withChangeHistory()
            ->build();
    }

    public function testExternalConsultantCommissionCost(): void
    {
        $cost = (new InternalCommercialCommissionCost(
            finalValue: 10000,
        ))->cost();

        $this->assertEquals(100, $cost);
    }
}
