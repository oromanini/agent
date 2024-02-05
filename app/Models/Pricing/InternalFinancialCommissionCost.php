<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class InternalFinancialCommissionCost extends BaseCost implements Cost
{
    public const KEY = 'commission_percentage';

    public function __construct(
        private readonly float $finalValue,
        private readonly int $paymentType
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if ($this->paymentType !== PaymentTypeEnum::FINANCING) {
            return 0;
        }

        return $this->finalValue * $this->commissionPercentage();
    }

    private function commissionPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::INTERNAL_FINANCING_COMMISSION;
    }
}
