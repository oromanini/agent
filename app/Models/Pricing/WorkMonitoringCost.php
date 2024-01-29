<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;
use App\Exceptions\CostRangeNotFound;

class WorkMonitoringCost extends BaseCost implements Cost
{
    protected const MAX_RANGE_POWER = 20;
    protected const MAX_RANGE_PERCENT = 0.03;
    protected const MINIMUM_MONITORING_COST = 160;

    public function __construct(private readonly float $kwp)
    {
        parent::__construct();
    }

    /** @throws CostRangeNotFound */
    public function cost(): float
    {
        if ($this->kwp >= self::MAX_RANGE_POWER) {
            return $this->calculateCostWithMaximumPowerRange();
        }

        $cost = $this->calculateRange(
            minimumCost: self::MINIMUM_MONITORING_COST,
            kwp: $this->kwp
        );
        $cost == 0 && $this->logAndCry();

        return $cost;
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::WORK_MONITORING;
    }

    protected function costRanges(): array
    {
        return $this->workCostInfo()['costs']['monitoring_cost_range'];
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
