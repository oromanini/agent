<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class TaxCost extends BaseCost implements Cost
{
    private const SELL_KEY = 'sell_estimated_percentage';
    private const SERVICE_KEY = 'service_estimated_percentage';

    public function __construct(
        private readonly float $costValue,
        private readonly float $finalValue,
        private readonly int $paymentType
    ) {
        parent::__construct();
    }

    public function cost(): float
    {
        if ($this->paymentType === PaymentTypeEnum::FINANCING) {
            return ($this->finalValue - $this->costValue)
                * $this->estimatedServicePercentage();
        }

        return $this->finalValue * $this->estimatedSellPercentage();
    }

    private function estimatedSellPercentage(): float
    {
        return $this->workCostInfo()['costs'][self::SELL_KEY];
    }

    private function estimatedServicePercentage(): float
    {
        return $this->workCostInfo()['costs'][self::SERVICE_KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::TAX;
    }
}
