<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class TaxCost extends BaseCost implements Cost
{
    public const SELL_KEY = 'sell_estimated_percentage';
    public const SERVICE_KEY = 'service_estimated_percentage';

    public function __construct(
        private readonly float $costValue,
        private readonly float $finalValue,
        private readonly int $paymentType
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if ($this->paymentType === PaymentTypeEnum::FINANCING) {
            return ($this->finalValue - $this->costValue)
                * $this->estimatedServicePercentage();
        }

        if ($this->paymentType === PaymentTypeEnum::CREDIT_CARD) {
            return ($this->finalValue - $this->costValue)
                * $this->estimatedSellPercentage();
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
