<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class DeliveryCost extends BaseCost implements Cost
{
    public const KEY = 'estimated_delivery_percentage';
    public const DELIVER_FREE_STATES = ['SÃO PAULO', 'PARANÁ'];

    public function __construct(
        private readonly float $kitCost,
        private readonly string $state
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        return $this->kitCost * $this->estimatedDeliveryPercent();
    }

    private function estimatedDeliveryPercent(): float
    {
        if (!in_array($this->state, self::DELIVER_FREE_STATES)) {
            return 300;
        }
        return 0;
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::DELIVERY;
    }
}
