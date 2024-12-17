<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class DirectCurrentCost extends BaseCost implements Cost
{
    const MINIMUM_CA_COST = 1000;
    const CA_FOR_LEAD = 900;
    public const KEY = 'estimated_material_percentage';

    public function __construct(
        private readonly float $finalValue,
        private readonly bool $isLead
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if ($this->isLead) {
            return self::CA_FOR_LEAD;
        }

        $cost = $this->finalValue * $this->estimatedMaterialPercentage();

        $cost < self::MINIMUM_CA_COST
        && $cost = self::MINIMUM_CA_COST;

        return $cost;
    }

    private function estimatedMaterialPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::DIRECT_CURRENT_MATERIAL;
    }
}
