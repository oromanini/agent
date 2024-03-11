<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;
use App\Exceptions\CostRangeNotFound;

class HomologationCost extends BaseCostWithRange implements Cost
{
    const MAX_RANGE_POWER = 30;
    const MAX_RANGE_PERCENT = 0.05;
    const MINIMUM_HOMOLOGATION_COST = 100;
    public const KEY = 'homologation_cost_range';

    public function __construct(private readonly float $kwp)
    {
        parent::__construct();
    }

    /** @throws CostRangeNotFound */
    public function cost(?float $getPercent = null): float
    {
        if ($this->kwp >= self::MAX_RANGE_POWER) {
            return $this->calculateCostWithMaximumPowerRange();
        }

        $range = $this->costRanges();
        $cost = self::MINIMUM_HOMOLOGATION_COST;
        foreach ($range as $rangeKwp => $rangeCost) {
            if ($this->kwp <= $rangeKwp) {
                $cost = $rangeCost;
                break;
            }
        }
        $cost === 0 && $this->logAndCry();

        return $cost;
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::HOMOLOGATION;
    }

    protected function costRanges(): array
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    private function calculateCostWithMaximumPowerRange(): float
    {
        $wP = $this->kwp * 1000;

        return self::MAX_RANGE_PERCENT * $wP;
    }

    private function missingRangeMessage(): string
    {
        return "A faixa para a classificação {$this->classification()} => {$this->kwp}
         kWP não foi encontrada!";
    }

    private function logAndCry(): void
    {
        $this->logMissingRange($this->missingRangeMessage());
        throw new CostRangeNotFound($this->missingRangeMessage());
    }
}
