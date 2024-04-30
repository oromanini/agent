<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class ExternalConsultantsCommissionCost extends BaseCost implements Cost
{
    public const STANDARD_KEY = 'commission_percentage';
    public const CREDIT_CARD_KEY = 'credit_card_commission_percentage';
    public const LEAD_COMMISSION = 0.04;

    public function __construct(
        private readonly float $finalValue,
        private readonly int $paymentType,
        private readonly bool $isLead
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if ($this->isLead) {
            return $this->finalValue * self::LEAD_COMMISSION;
        }

        if ($this->paymentType === PaymentTypeEnum::CREDIT_CARD) {
            return $getPercent
                ? $this->creditCardCommissionPercentage()
                : $this->finalValue * $this->creditCardCommissionPercentage();
        }

        return $getPercent
            ? $this->standardCommissionPercentage()
            : $this->finalValue * $this->standardCommissionPercentage();
    }

    private function standardCommissionPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::STANDARD_KEY];
    }

    private function creditCardCommissionPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::CREDIT_CARD_KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION;
    }
}
