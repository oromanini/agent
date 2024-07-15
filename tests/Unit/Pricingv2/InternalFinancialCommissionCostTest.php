<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Pricing\InternalFinancialCommissionCost;
use Tests\TestCase;

class InternalFinancialCommissionCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->costs = [
            'commission_percentage' => 0.01,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::INTERNAL_FINANCING_COMMISSION)
            ->withChangeHistory()
            ->build();
    }

    public function testExternalConsultantCommissionCost(): void
    {
        $cost = (new InternalFinancialCommissionCost(
            finalValue: 100000,
            paymentType: PaymentTypeEnum::CASH_PAYMENT
        ))->cost();

        $this->assertEquals(0, $cost);
    }

    public function testExternalConsultantCommissionCost_WithCreditCard(): void
    {
        $cost = (new InternalFinancialCommissionCost(
            finalValue: 100000,
            paymentType: PaymentTypeEnum::FINANCING
        ))->cost();

        $this->assertEquals(1000, $cost);
    }
}
