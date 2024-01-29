<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class ExternalConsultantsCommissionCost extends BaseCost implements Cost
{
    private const STANDARD_KEY = 'commission_percentage';
    private const CREDIT_CARD_KEY = 'credit_card_commission_percentage';

    public function __construct(
        private readonly float $finalValue,
        private readonly int $paymentType
    ) {
        parent::__construct();
    }

    public function cost(): float
    {
        if ($this->paymentType === PaymentTypeEnum::CREDIT_CARD) {
            return $this->finalValue * $this->creditCardCommissionPercentage();
        }

        return $this->finalValue * $this->standardCommissionPercentage();
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
