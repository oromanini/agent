<?php


namespace App\Services;

use App\Enums\NorthStates;
use App\Enums\RoofStructure;
use App\Models\Address;
use App\Models\Client;
use App\Models\PromotionalKit;

class PricingService
{
    const BASE_GROSS_PROFIT = 1.6;
    const SOLO_PLUS = 1.35;
    const LIQUID_PROFIT_PERCENTAGE = 0.12;
    const PLUS_TO_ADJUST_MARGIN = 250;
    const HOMOLOGATION_COST_ABOVE_90_KWP = 0.025;
    const CA_COST_PERCENT =  0.045;
    const CA_MINIMUN_COST = 1000;
    const DELIVERY_FEE = 0.06;
    const INSTALLATION_MINIMUM_COST = 1000;
    private float $netProfit = 0.14;

    public function calculateFinalPrice(array $data): array
    {
        $finalValue = $this->adjustMargin(
            cost: $data['cost'],
            kwp: (float)$data['kwp'],
            panelCount: $data['panel_count'],
            finalValue: $data['cost'] * self::BASE_GROSS_PROFIT,
            stateId: $this->setStateId($data)
        );

        if ($data['roof_structure'] == RoofStructure::SOLO) {
            return ['finalPrice' => $finalValue * self::SOLO_PLUS, 'isPromotional' => false];
        }

        return $this->findOrFailPromotionalKits(params: $data, finalPrice: $finalValue);
    }

    private function adjustMargin(
        float $cost,
        float $kwp,
        int $panelCount,
        float $finalValue,
        int $stateId
    ): float {
        $this->netProfit =
            $this->calculateNetProfit(
                cost: $cost,
                kwp: $kwp,
                panelCount: $panelCount,
                finalValue: $finalValue,
                stateId: $stateId
            )['netProfitPercent'];

        if ($this->netProfit < self::LIQUID_PROFIT_PERCENTAGE) {
            $finalValue += self::PLUS_TO_ADJUST_MARGIN;
            $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue, $stateId);
        }

        return $finalValue;
    }

    function calculateHomologation(float $kwp, float $finalValue): float
    {
        foreach ($this->getHomologationPrices() as $kwpLimit => $homologationValue) {
            if ($kwp <= $kwpLimit) {
                return $homologationValue;
            }
        }

        return $finalValue * self::HOMOLOGATION_COST_ABOVE_90_KWP;
    }


    private function calculateNetProfit(float $cost, float $kwp, int $panelCount, float $finalValue, int $stateId): array
    {
        $installation = $this->calculateInstallation($panelCount);
        $delivery = $this->calculateDelivery($finalValue, $stateId);
        $homologation = $this->calculateHomologation($kwp, $finalValue);
        $ca = $this->calculateCa($finalValue);
        $tax = $finalValue * env('TAX_PERCENT');
        $commission = $finalValue * env('COMMISSION_PERCENT');

        $totalCost = $cost + $installation + $homologation + $ca + $tax + $commission + $delivery;
        $netProfit = $finalValue - $totalCost;
        $netProfitPercent = ($finalValue / $totalCost) - 1;

        return ['netProfit' => $netProfit, 'netProfitPercent' => $netProfitPercent, 'totalCost' => $totalCost];
    }

    public function calculateCa(float $finalValue): float
    {
        $ca = $finalValue * self::CA_COST_PERCENT;

        return max($ca, self::CA_MINIMUN_COST);
    }

    private function calculateDelivery(float $finalValue, int $stateId): float
    {
        $isExpensiveState = NorthStates::hasValue($stateId);

        if ($isExpensiveState) {
            return $finalValue * self::DELIVERY_FEE;
        }

        return 0;
    }

    private function  setStateId(array $data): int
    {
        if (isset($data['client'])) {
            return Client::find((int)$data['client'])
                ->addresses
                ->first()
                ->city
                ->state
                ->id;
        }

        return Address::find((int)$data['address_id'])
            ->city
            ->state
            ->id;
    }

    private function calculateInstallation(int $panelCount): float
    {
        $installation = $panelCount * env('INSTALLATION_PANEL_PRICE');

        return max($installation, self::INSTALLATION_MINIMUM_COST);
    }

    public function findOrFailPromotionalKits(array $params, float $finalPrice): array
    {
        $promotionalKits = PromotionalKit::where('is_active', true)->get();
        $isPromotional = false;

        $promotionalKits->each(function ($promotion) use ($params, &$finalPrice, &$isPromotional) {
            if($this->kitMatchPromotion($promotion, $params)) {
                $finalPrice = $promotion->final_value;
                $isPromotional = true;
            }
        });

        return ['finalPrice' => $finalPrice, 'isPromotional' => $isPromotional];
    }

    private function kitMatchPromotion(PromotionalKit $promotion, array $params): bool
    {
        if (
            $params['kwp'] == $promotion->kwp
            && strtolower($params['panelBrand']) == $promotion->panel_brand
            && strtolower($params['panelPower']) == $promotion->panel_power
            && strtolower($params['inverterBrand']) == $promotion->inverter_brand
        ) {
            return true;
        }

        return false;
    }

    private function getHomologationPrices(): array
    {
        return [
            15 => 600,
            30 => 900,
            45 => 1200,
            60 => 1500,
            75 => 2000,
            90 => 2500,
        ];
    }
}
