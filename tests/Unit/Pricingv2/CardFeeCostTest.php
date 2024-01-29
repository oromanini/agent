<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\CardFeeCost;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use Tests\TestCase;

class CardFeeCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->costs = ['estimated_fee' => 0.114];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::CARD_FEE)
            ->withChangeHistory()
            ->build();
    }

    public function testCardFeeCost(): void
    {
        $cardFee = new CardFeeCost(
            finalValue: 1000,
            paymentType: PaymentTypeEnum::CREDIT_CARD
        );
        $this->assertEquals(114, $cardFee->cost());
    }
}
