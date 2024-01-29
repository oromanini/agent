<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class DeliveryCost extends BaseCost implements Cost
{
    private const KEY = 'estimated_delivery_percentage';

    public function __construct(
        private readonly float $kitCost
    ) {
        parent::__construct();
    }

    public function cost(): float
    {
        return $this->kitCost * $this->estimatedDeliveryPercent();
    }

    private function estimatedDeliveryPercent(): float
    {
        if (!$this->workCostInfo()['costs']['enabled']) {
            return 0;
        }

        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::DELIVERY;
    }
}
