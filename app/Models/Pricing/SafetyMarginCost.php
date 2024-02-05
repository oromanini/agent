<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class SafetyMarginCost extends BaseCost implements Cost
{
    public const KEY = 'estimated_percentage';

    public function __construct(
        private readonly float $finalValue
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        return $this->finalValue * $this->estimatedPercentage();
    }

    private function estimatedPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::SAFETY_MARGIN;
    }
}
