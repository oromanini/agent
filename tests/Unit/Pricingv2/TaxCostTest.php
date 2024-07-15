<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use App\Models\Pricing\TaxCost;
use Tests\TestCase;

class TaxCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->costs = [
            'sell_estimated_percentage' => 0.06,
            'service_estimated_percentage' => 0.135,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::TAX)
            ->withChangeHistory()
            ->build();
    }

    public function testTaxCost(): void
    {
        $tax = new TaxCost(
            costValue: 12000,
            finalValue: 20000,
            paymentType: PaymentTypeEnum::CASH_PAYMENT
        );
        $this->assertEquals(1200, $tax->cost());
    }

    public function testTaxCost_WithFinancingPayment(): void
    {
        $tax = new TaxCost(
            costValue: 12000,
            finalValue: 20000,
            paymentType: PaymentTypeEnum::FINANCING
        );
        $this->assertEquals(1080, $tax->cost());
    }
}
