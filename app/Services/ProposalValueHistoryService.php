<?php

namespace App\Services;

use App\Models\ProposalValueHistory;
use Illuminate\Support\Facades\DB;

class ProposalValueHistoryService
{
    public function store($data, bool $isManual): int
    {
        $valueHistory = new ProposalValueHistory();

        $valueHistory->kit_cost = $isManual ? stringMoneyToFloat($data['cost']) : $data['sumKits']['cost'];
        $finalPrice = $isManual ? stringMoneyToFloat($data['final_value']) : setFinalPrice($data);
        $valueHistory->initial_price = $finalPrice;
        $valueHistory->final_price = $finalPrice;

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

        if (isset($data['discount_percent'])) {

            $valueHistory->discount_percent = (float)$data['discount_percent'];

            $discount = $valueHistory->initial_price * ((float)$data['discount_percent'] / 100);

            $commissionPercent = $valueHistory->commission_percent / 100;

            $calculationBasis = $initialPrice - $discount;

            $fullCommissionValue = $calculationBasis * $fullCommissionPercent;

            $commissionDiscount = $fullCommissionValue - ($calculationBasis * $commissionPercent);

            $valueHistory->final_price = round($initialPrice - $discount - $commissionDiscount, 2);
        }

        if (isset($data['commission_percent'])) {

            $valueHistory->commission_percent = (float)$data['commission_percent'];
            $commissionPercent = (float)$data['commission_percent'] / 100;
            $discount = $valueHistory->initial_price * ($valueHistory->discount_percent / 100);

            $calculationBasis = $initialPrice - $discount;
            $fullCommissionValue = $calculationBasis * $fullCommissionPercent;
            $newCommissionValue = $calculationBasis * $commissionPercent;
            $commissionDiscount = $fullCommissionValue - $newCommissionValue;

            $valueHistory->final_price = round($initialPrice - $discount - $commissionDiscount, 2);
        }

        DB::transaction(function () use ($valueHistory) {
            $valueHistory->update();
        });

        return ['success', 'Alteração de valor aplicada!'];
    }

}
