<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use Tests\TestCase;

class ExternalConsultantsCommissionCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->costs = [
            'commission_percentage' => 0.1,
            'credit_card_commission_percentage' => 0.08,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION)
            ->withChangeHistory()
            ->build();
    }

    public function testExternalConsultantCommissionCost(): void
    {
        $cost = (new ExternalConsultantsCommissionCost(
            finalValue: 10000,
            paymentType: PaymentTypeEnum::CASH_PAYMENT
        ))->cost();

        $this->assertEquals(1000, $cost);
    }

    public function testExternalConsultantCommissionCost_WithCreditCard(): void
    {
        $cost = (new ExternalConsultantsCommissionCost(
            finalValue: 10000,
            paymentType: PaymentTypeEnum::CREDIT_CARD
        ))->cost();

        $this->assertEquals(800, $cost);
    }
}
