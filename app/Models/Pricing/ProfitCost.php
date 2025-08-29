<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class ProfitCost extends BaseCost implements Cost
{
    public const STANDARD_KEY = 'profit';

    public function __construct(
        private readonly float $finalValue,
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        return $this->finalValue * $this->percent();
    }

    public function percent(): float
    {
        return (float) $this->workCostInfo()['costs'][self::STANDARD_KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::PROFIT;
    }
}
