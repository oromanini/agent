<?php

namespace App\Builders;

use App\Models\ProposalValueHistory;

class ValueHistoryBuilder implements Builder
{
    private ProposalValueHistory $valueHistory;

    public function __construct()
    {
        $this->valueHistory = new ProposalValueHistory();
    }

    public function withKitCost(float $cost): self
    {
        $this->valueHistory->kit_cost = $cost;
        return $this;
    }

    public function withInitialAndFinalPrice(
        float $financingPrice,
        float $cashPrice,
        float $cardPrice,
    ): self {
        $this->valueHistory->initial_price = $financingPrice;
        $this->valueHistory->cash_initial_price = $cashPrice;
        $this->valueHistory->card_initial_price = $cardPrice;

        $this->valueHistory->final_price = $financingPrice;
        $this->valueHistory->card_final_price = $cashPrice;
        $this->valueHistory->cash_final_price = $cardPrice;

        return $this;
    }

    public function withIsPromotional(bool $isPromotional): self
    {
        $this->valueHistory->is_promotional = $isPromotional;
        return $this;
    }

    public function withCommissionPercent(array $commissionPercent): self
    {
        $this->valueHistory->commission_percent = 0;
        $this->valueHistory->commission = json_encode($commissionPercent);

        return $this;
    }

    public function withDiscountPercent(float $discountPercent): self
    {
        $this->valueHistory->discount_percent = $discountPercent;
        return $this;
    }

    public function withAuthUser(int $id): self
    {
        $this->valueHistory->user_id = $id;
        return $this;
    }

    public function build(): ProposalValueHistory
    {
        $this->valueHistory->save();
        return $this->valueHistory;
    }
}
