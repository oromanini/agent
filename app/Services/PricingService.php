<?php


namespace App\Services;

use App\Enums\NorthStates;
use App\Models\Address;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class PricingService
{
    const SOLO = 6;
    const SOLO_MARGIN = 1.3;
    const BASE_PROFIT = 1.45;
    const DELIVERY_TAX = 0.06;


    public function calculateFinalPrice(array $data): float
    {
        $cost = isset($data['sumKits']) ? (float)$data['sumKits']['cost'] : (float)$data['cost'];
        $kwp = isset($data['sumKits']) ? $data['sumKits']['kwp'] : (float)$data['kwp'];
        $panelCount = isset($data['sumKits']) ? $data['sumKits']['panel_count'] : (int)$data['panel_count'];
        $finalValue = $cost * self::BASE_PROFIT;
        $stateId = $this->setStateId($data);

        $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue, $stateId);

        if ($data['roof_structure'] == self::SOLO) {
            return $finalValue * self::SOLO_MARGIN;
        }

        if(self::isPromotionalKit($kwp, $data['promo_data'])) {
            return $this->getPromotionalKitValue();
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

    function calculateHomologation(float $kwp, float $finalValue): float
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
        $installation = $this->calculateInstallation($panelCount);
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
        $ca = $finalValue * 0.045;

        return max($ca, 750);
    }

    private function calculateDelivery(float $finalValue, int $stateId): float
    {
        if (NorthStates::hasValue($stateId)) {
            return $finalValue * self::DELIVERY_TAX;
        }

        return $finalValue * env('DELIVERY_PERCENT');
    }

    private function setStateId(array $data): int
    {
        if (isset($data['client'])) {
            return Client::find((int)$data['client'])->addresses->first()->city->state->id;
        }

        return Address::find((int)$data['address_id'])->city->state->id;
    }

    private function calculateInstallation(int $panelCount): float
    {
        $installation = $panelCount * env('INSTALLATION_PANEL_PRICE');

        return max($installation, 700);
    }

    private static function isPromotionalKit(
        float $kwp,
        array $promoData
    ): bool {
        dump($kwp);
        $kit = DB::table('promotional_kits')
            ->where('kwp', $kwp)->get();
//            ->where('inverter_brand', strtolower($promoData['inverter_brand']))
//            ->where('panel_brand', strtolower($promoData['panel_brand']))
//            ->where('panel_power', $promoData['panel_power'])
        dd($kit);
        return (bool) $kit;
    }
}
