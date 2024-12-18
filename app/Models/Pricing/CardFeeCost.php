<?php

namespace App\Models\Pricing;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\Enums\CardTaxEnum;

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

        return CardTaxEnum::calculateInstallments($this->finalValue)[18];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::CARD_FEE;
    }
}
