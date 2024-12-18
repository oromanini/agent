<?php

namespace App\Services;

use App\Models\Pricing\CardFeeCost;
use App\Models\Pricing\Cost;
use App\Models\Pricing\DeliveryCost;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Pricing\HomologationCost;
use App\Models\Pricing\InstallationCost;
use App\Models\Pricing\InternalCommercialCommissionCost;
use App\Models\Pricing\InternalFinancialCommissionCost;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use App\Models\Pricing\TaxCost;
use App\Models\Pricing\WorkMonitoringCost;

class TotalCostForCreditCard implements Cost
{
    public function __construct(
        private readonly float $cost,
        private readonly int $panelCount,
        private readonly float $kwp,
        private readonly float $finalValue,
        private readonly int $paymentType,
        private readonly bool $isLead,
        private readonly string $state
    ) {
    }

    public function cost(?float $getPercent = null): float
    {
        return $this->cost
            + (new InstallationCost($this->panelCount, $this->isLead, $this->state))->cost()
            + (new HomologationCost($this->kwp))->cost()
            + (new WorkMonitoringCost($this->kwp))->cost()
            + (new DirectCurrentCost($this->finalValue, $this->isLead))->cost()
            + (new DeliveryCost($this->cost, $this->state))->cost()
            + (new ExternalConsultantsCommissionCost($this->finalValue, $this->paymentType, $this->isLead))->cost()
            + (new InternalCommercialCommissionCost($this->finalValue, $this->isLead))->cost()
            + (new SafetyMarginCost($this->finalValue))->cost()
            + (new RoyaltyCost($this->finalValue))->cost()
            + (new TaxCost($this->cost, $this->finalValue, $this->paymentType))->cost()
            + (new CardFeeCost($this->finalValue, $this->paymentType))->cost()
            ;
    }
}
