<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;

class CardFeeCost extends BaseCost implements Cost
{
    public const KEY = 'estimated_fee';

    public function __construct(
        private readonly float $finalValue,
        private readonly int $paymentType
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        if (!$this->paymentType == PaymentTypeEnum::CREDIT_CARD) {
            return 0;
        }

        return $this->finalValue * $this->estimatedCardFee();
    }

    private function estimatedCardFee(): float
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::CARD_FEE;
    }
}
