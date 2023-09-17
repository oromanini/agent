<?php

namespace App\Services;

use App\Enums\TensionPattern;
use App\Models\Address;
use App\Models\Kit;
use App\Models\Proposal;

class KitSpecService
{
    const INVERTER_OVERLOAD = 1.35;

    public function setAverageProduction(array $data): float
    {
        $city = Address::find((int)$data['addressId'])->city;

        $incidence = (float)str_replace(
            search: ',',
            replace: '.',
            subject: (new SolarIncidenceService())->getSolarIncidence($city)->average
        );

        return ceil(
            ((float) $data['kwp']) / ((1 + (float)env('GENERATION_LOST')))
            * 30
            * $incidence
        );
    }

    public function setTensionByValue(array $data): string
    {
        return TensionPattern::tryFrom($data['tension'])->name;
    }

    public function getKitFromProposal(Proposal $proposal): Kit
    {
        return Kit::where('distributor_code', $proposal->kit_uuid)->first();
    }

    public function getKitOverload(Kit $kit = null, array $manualData = null): int
    {
        if (!is_null($manualData)) {
            return (stringInverterPowerToFloat($manualData['inverter_power']) * 1.35) / ((int)$manualData['panel_power'] / 1000);
        }

        $panelPower = jsonToArray($kit->panel_specs)['power'] / 1000;
        $inverterPower = jsonToArray($kit->inverter_specs)['power'];

        $overload = $inverterPower * self::INVERTER_OVERLOAD;

        return roundOrFloorDecimalNumber($overload / $panelPower);
    }

    public function setInvertersCount(array $components): string
    {
        $inverter_count = 0;

        array_map(function ($item) use (&$inverter_count) {
            (str_contains($item, 'INVERSOR')) && $inverter_count++;
        }, $components);

        return $inverter_count;
    }

    public function setInverterModels(array $components): string
    {
        $inverter_models = [];

        array_map(function ($item) use (&$inverter_models) {
            (str_contains($item, 'Inversor')) && $inverter_models[] = $item;
        }, $components);

        $string = '';

        foreach ($inverter_models as $key => $inverter_model) {
            $string .= $key == 0
                ? explode(' ', $inverter_model)[3]
                : ' + ' . explode(' ', $inverter_model)[3];
        }

        return $string;
    }
}
