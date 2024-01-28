<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;
use App\Repositories\WorkCostRepository;

class InstallationCost implements Cost
{
    private WorkCostRepository $workCostRepository;

    public function __construct(
        private readonly int $panelQuantity
    )
    {
        $this->workCostRepository = new WorkCostRepository();
    }

    public function cost(): float
    {
        return $this->panelQuantity * $this->panelPrice();
    }

    public function workCostInfo(): array
    {
        return $this->workCostRepository
            ->getWorkCostByClassification(
                WorkCostClassificationEnum::INSTALLATION
            )->toArray();
    }

    private function panelPrice(): float
    {
        return $this->workCostInfo()['costs']['panel_price'];
    }
}
