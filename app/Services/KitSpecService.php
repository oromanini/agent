<?php

namespace App\Services;

use App\Enums\TensionPattern;
use App\Models\Address;

class KitSpecService
{
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

    private function getKitOverload(array $codes): int
    {
        return array_map(function ($code) {
            $kit = kitByUuid($code);

            return floor(
                $kit['technical_description']['inverter_overload']
                / ($kit['technical_description']['panel_specs']['panel_power'] / 1000)
            );

        }, $codes)[0];
    }

    private function setInvertersCount(array $components): string
    {
        $inverter_count = 0;

        array_map(function ($item) use (&$inverter_count) {
            (str_contains($item, 'Inversor')) && $inverter_count++;
        }, $components);

        return $inverter_count;
    }

    private function setInverterModels(array $components): string
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
