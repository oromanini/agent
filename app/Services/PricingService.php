<?php


namespace App\Services;

use App\Enums\NorthStates;
use App\Models\Address;
use App\Models\Client;

class PricingService
{
    public function calculateFinalPrice(array $data): float
    {
        $cost = isset($data['sumKits']) ? (float)$data['sumKits']['cost'] : (float)$data['cost'];
        $kwp = isset($data['sumKits']) ? $data['sumKits']['kwp'] : (float)$data['kwp'];
        $panelCount = isset($data['sumKits']) ? $data['sumKits']['panel_count'] : (int)$data['panel_count'];
        $finalValue = $cost * 1.45;
        $stateId = $this->setStateId($data);

        $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue, $stateId);

        if ($data['roof_structure'] == 6) {
            return $finalValue * 1.3;
        }

//        if ($kwp == 2.77) {
//            return 13000;
//        }
//        if ($kwp == 3.88) {
//            return 16900;
//            }
//        if ($kwp == 6.66) {
//            return 23450;
//            }
//        if ($kwp == 7.77) {
//            return 26900    ;
//            }

        return $finalValue;
    }

    private function adjustMargin(float $cost, float $kwp, int $panelCount, float $finalValue, int $stateId): float
    {
        while ($this->calculateNetProfit($cost, $kwp, $panelCount, $finalValue, $stateId)['netProfitPercent'] < 0.14) {
            $finalValue += 250;
        }

        return $finalValue;
    }

    function calculateHomologation(float $kwp, float $finalValue)
    {
        $homologation = 0;

        if ($kwp <= 15) {
            $homologation = 600;
        } elseif ($kwp <= 30) {
            $homologation = 900;
        } elseif ($kwp <= 45) {
            $homologation = 1200;
        } elseif ($kwp <= 60) {
            $homologation = 1500;
        } elseif ($kwp <= 75) {
            $homologation = 2000;
        } elseif ($kwp <= 90) {
            $homologation = 2500;
        } else {
            $homologation = $finalValue * 0.025;
        }

        return $homologation;
    }

    private function calculateNetProfit(float $cost, float $kwp, int $panelCount, float $finalValue, int $stateId): array
    {
        $installation = $panelCount * env('INSTALLATION_PANEL_PRICE');
        $delivery = $this->calculateDelivery($finalValue, $stateId);
        $homologation = $this->calculateHomologation($kwp, $finalValue);
        $ca = $this->calculateCa($finalValue, $kwp);
        $tax = $finalValue * env('TAX_PERCENT');
        $commission = $finalValue * env('COMMISSION_PERCENT');

        $totalCost = $cost + $installation + $homologation + $ca + $tax + $commission + $delivery;
        $netProfit = $finalValue - $totalCost;
        $netProfitPercent = ($finalValue / $totalCost) - 1;

        return ['netProfit' => $netProfit, 'netProfitPercent' => $netProfitPercent, 'totalCost' => $totalCost];
    }

    public function calculateCa(float $finalValue, float $kwp): float
    {
        if ($kwp <= 4) {
            return $finalValue * 0.04;
        } elseif ($kwp <= 10) {
            return $finalValue * 0.035;
        } else {
            return $finalValue * 0.03;
        }
    }

    private function calculateDelivery(float $finalValue, int $stateId): float
    {
        $isExpensiveState = NorthStates::hasValue($stateId);

        if ($isExpensiveState) {
            return $finalValue * 0.06;
        }

        return $finalValue * env('DELIVERY_PERCENT');
    }

    private function setStateId(array $data)
    {
        if (isset($data['client'])) {
            return Client::find((int)$data['client'])->addresses->first()->city->state->id;
        }

        return Address::find((int)$data['address_id'])->city->state->id;
    }
}
