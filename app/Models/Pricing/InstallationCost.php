<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class InstallationCost extends BaseCost implements Cost
{
    private const KEY = 'panel_price';

    public function __construct(
        private readonly int $panelQuantity
    ) {
        parent::__construct();
    }

    public function cost(): float
    {
        return $this->panelQuantity * $this->panelPrice();
    }

    private function panelPrice(): float
    {
        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::INSTALLATION;
    }
}
