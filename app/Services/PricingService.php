<?php

namespace App\Services;

class PricingService
{
    public function calculateFinalPrice($data): float
    {
        $cost = isset($data['sumKits']) ? (float)$data['sumKits']['cost'] : (float)$data['cost'];
        $kwp = isset($data['sumKits']) ? $data['sumKits']['kwp'] : (float)$data['kwp'];
        $panelCount = isset($data['sumKits']) ? $data['sumKits']['panel_count'] : (int)$data['panel_count'];
        $finalValue = $cost * 1.45;

        $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue);

        if ($data['roof_structure'] == 6) {
            return $finalValue * 1.3;
        }

        return $finalValue;
    }

    private function adjustMargin($cost, $kwp, $panelCount, $finalValue): float
    {
        while ($this->calculateNetProfit($cost, $kwp, $panelCount, $finalValue)['netProfitPercent'] < 0.14) {
            $finalValue += 250;
        }

        return $finalValue;
    }

    function calculateHomologation($kwp, $finalValue)
    {
        $homologation = 0;

        if ($kwp <= 15) {
            $homologation = 350;
        } elseif ($kwp > 15 && $kwp <= 30) {
            $homologation = 700;
        } elseif ($kwp > 30 && $kwp <= 45) {
            $homologation = 1000;
        } elseif ($kwp > 45 && $kwp <= 60) {
            $homologation = 1300;
        } elseif ($kwp > 60 && $kwp <= 75) {
            $homologation = 1600;
        } elseif ($kwp > 75 && $kwp <= 90) {
            $homologation = 1900;
        } else {
            $homologation = $finalValue * 0.025;
        }

        return $homologation;
    }

    private function calculateNetProfit($cost, $kwp, $panelCount, $finalValue): array
    {
        $installation = $panelCount * env('INSTALLATION_PANEL_PRICE');
        $delivery = $finalValue * env('DELIVERY_PERCENT');
        $homologation = $this->calculateHomologation($kwp, $finalValue);
        $ca = $this->calculateCa($finalValue, $kwp);
        $tax = $finalValue * env('TAX_PERCENT');
        $commission = $finalValue * env('COMMISSION_PERCENT');

        $totalCost = $cost + $installation + $homologation + $ca + $tax + $commission + $delivery;
        $netProfit = $finalValue - $totalCost;
        $netProfitPercent = ($finalValue / $totalCost) - 1;

        return ['netProfit' => $netProfit, 'netProfitPercent' => $netProfitPercent, 'totalCost' => $totalCost];
    }

    public function calculateCa($finalValue, $kwp): float
    {
        if ($kwp <= 4) {
            return $finalValue * 0.04;
        } elseif ($kwp <= 10) {
            return $finalValue * 0.035;
        } else {
            return $finalValue * 0.03;
        }
    }
}
