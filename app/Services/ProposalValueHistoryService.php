<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use Illuminate\Support\Facades\DB;

class ProposalValueHistoryService
{
    public function __construct(private readonly PricingService $pricingService)
    {
    }

    public function store($data, bool $isManual): int
    {
        $valueHistory = new ProposalValueHistory();

        $valueHistory->kit_cost = $this->getKitCost($data, $isManual);
        $finalPrice = $this->getFinalPrice($data, $isManual);

        $valueHistory->initial_price = $isManual
            ? $finalPrice
            : $finalPrice['finalPrice'];

        $valueHistory->final_price = $isManual
            ? $finalPrice
            : $finalPrice['finalPrice'];

        $valueHistory->is_promotional = false;

        $valueHistory->commission_percent = env('COMMISSION_PERCENT') * 100;
        $valueHistory->discount_percent = 0;
        $valueHistory->user_id = auth()->user()->id;

        DB::transaction(function () use ($valueHistory) {
            $valueHistory->save();
        });

        return $valueHistory->id;
    }

    public function update($valueHistory, $data): array
    {
        $initialPrice = $valueHistory->initial_price;
        $fullCommissionPercent = (float)env('COMMISSION_PERCENT');

        isset($data['discount_percent']) && $valueHistory =
            $this->updateWithDiscountPercent(
                $valueHistory,
                $data,
                $initialPrice,
                $fullCommissionPercent
            );

        isset($data['commission_percent']) && $valueHistory =
            $this->updateWithCommissionPercent(
                $valueHistory,
                $data,
                $initialPrice,
                $fullCommissionPercent
            );

        DB::transaction(function () use ($valueHistory) {
            $valueHistory->update();
        });

        return ['success', 'Alteração de valor aplicada!'];
    }

    public function setValueHistoryData(Proposal $proposal): array
    {
        $valueHistory = $proposal->valueHistory;

        $discountPercent = $valueHistory->discount_percent / 100;
        $discountValue = $valueHistory->initial_price * $discountPercent;
        $calculateBase = $valueHistory->initial_price - $discountValue;

        $initialCommission = $calculateBase * ((float)env('COMMISSION_PERCENT'));
        $commissionPercent = $valueHistory->commission_percent;
        $commissionValue = $calculateBase * ($commissionPercent / 100);
        $commissionDiscountValue = $initialCommission - $commissionValue;

        $grossProfit = ($valueHistory->final_price / $proposal->valueHistory->kit_cost) - 1;

        $totalCost = $this->setTotalCost($proposal);

        return [
            'discountValue' => $discountValue,
            'calculateBase' => $calculateBase,
            'initialCommission' => $initialCommission,
            'finalCommission' => $commissionValue,
            'commissionDiscountValue' => $commissionDiscountValue,
            'cost' => floatToMoney($proposal->valueHistory->kit_cost),
            'gross_profit' => $grossProfit,
            'totalCost' => $totalCost,
        ];
    }

    private function setTotalCost(Proposal $proposal): array
    {
        $installation = $proposal->number_of_panels * env('INSTALLATION_PANEL_PRICE');
        $homologation = $this->pricingService->calculateHomologation($proposal->kwp, $proposal->valueHistory->final_price);
        $ca = $this->pricingService->calculateCa($proposal->valueHistory->final_price, $proposal->kwp);
        $tax = $proposal->valueHistory->final_price * env('TAX_PERCENT');
        $commission = ($proposal->valueHistory->commission_percent / 100) * $proposal->valueHistory->final_price;
        $servicesCost = $installation + $homologation + $ca + $tax + $commission;
        $netProfitValue = $proposal->valueHistory->final_price - ($proposal->valueHistory->kit_cost + $servicesCost);
        $netProfitPercent = $netProfitValue / $proposal->valueHistory->final_price;

        return [
            'installation' => $installation,
            'homologation' => $homologation,
            'ca' => $ca,
            'tax' => $tax,
            'commission' => $commission,
            'net_profit_value' => $netProfitValue,
            'net_profit_percent' => $netProfitPercent,
            'services_cost' => $servicesCost
        ];
    }

    private function getKitCost(array $data, bool $isManual): float
    {
        return $isManual
            ? stringMoneyToFloat($data['cost'])
            : $data['cost'];
    }

    private function getFinalPrice($data, bool $isManual): float|array
    {
        if ($isManual) {
            return stringMoneyToFloat($data['final_value']);
        }

        $pricingService = new PricingService();
        return $pricingService->calculateFinalPrice($data);
    }

    private function updateWithDiscountPercent(
        ProposalValueHistory $valueHistory,
        array                $data,
        float                $initialPrice,
        float                $fullCommissionPercent
    ): ProposalValueHistory {

        $valueHistory->discount_percent = (float)$data['discount_percent'];

        $discount = $valueHistory->initial_price * ((float)$data['discount_percent'] / 100);
        $commissionPercent = $valueHistory->commission_percent / 100;
        $calculationBasis = $initialPrice - $discount;
        $fullCommissionValue = $calculationBasis * $fullCommissionPercent;
        $commissionDiscount = $fullCommissionValue - ($calculationBasis * $commissionPercent);

        $valueHistory->final_price = round($initialPrice - $discount - $commissionDiscount, 2);

        return $valueHistory;
    }

    private function updateWithCommissionPercent(
        ProposalValueHistory $valueHistory,
        array                $data,
        float                $initialPrice,
        float                $fullCommissionPercent
    ): ProposalValueHistory {

        $valueHistory->commission_percent = (float)$data['commission_percent'];

        $commissionPercent = (float) $data['commission_percent'] / 100;
        $discount = $valueHistory->initial_price * ($valueHistory->discount_percent / 100);
        $calculationBasis = $initialPrice - $discount;
        $fullCommissionValue = $calculationBasis * $fullCommissionPercent;
        $newCommissionValue = $calculationBasis * $commissionPercent;
        $commissionDiscount = $fullCommissionValue - $newCommissionValue;

        $valueHistory->final_price = round($initialPrice - $discount - $commissionDiscount, 2);
        return $valueHistory;
    }
}
