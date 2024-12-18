<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class InternalCommercialCommissionCost extends BaseCost implements Cost
{
    public const KEY = 'commission_percentage';

    public function __construct(
        private readonly float $finalValue,
        private readonly bool $isLead
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if ($this->isLead) {
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
        return WorkCostClassificationEnum::INTERNAL_COMMERCIAL_COMMISSION;
    }
}
