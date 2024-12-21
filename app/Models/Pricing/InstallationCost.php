<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;

class InstallationCost extends BaseCost implements Cost
{
    public const KEY = 'panel_price';
    public const LEAD_KEY = 'lead_panel_price';

    public function __construct(
        private readonly float $panelQuantity,
        private readonly bool $isLead,
        private readonly string $state
    ) {
        parent::__construct();
    }

    public function cost(?float $getPercent = null): float
    {
        return $this->panelQuantity * $this->panelPrice();
    }

    private function panelPrice(): float
    {
        if ($this->isLead) {
            return $this->workCostInfo()['costs'][self::LEAD_KEY];
        }

        if ($this->state == 'SÃO PAULO') {
            return 150;
        }

        return $this->workCostInfo()['costs'][self::KEY];
    }

    protected function classification(): int
    {
        return WorkCostClassificationEnum::INSTALLATION;
    }
}
