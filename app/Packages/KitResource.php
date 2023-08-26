<?php

namespace App\Packages;

use Carbon\Carbon;

abstract class KitResource
{
    protected static function sanitizeToDefaultProperties(
        string $description,
        float $cost,
        string $roof_structure,
        string $distributor_name,
        string $distributor_code,
        Carbon $availability,
        float $kwp,
        string $panel_model,
        string $panel_brand,
        int $panel_power,
        int $panel_warranty,
        float $panel_efficiency,
        string $panel_logo,
        int $panel_linear_warranty,
        string $inverter_model,
        string $inverter_brand,
        int $inverter_power,
        int $inverter_warranty,
        string $inverter_logo,
        string $inverter_tension,
        array $components
    ): array {

        return [
            'description' => $description,
            'cost' => $cost,
            'roof_structure' => $roof_structure,
            'distributor_name' => strtoupper($distributor_name),
            'distributor_code' => $distributor_code,
            'availability' => !$availability->isPast() ? $availability->format('d/m/Y') : 'Imediata',
            'kwp' => $kwp,
            'components' => $components,
            'panel_specs' => [
                'model' => $panel_model,
                'brand' => $panel_brand,
                'power' => $panel_power,
                'warranty' => $panel_warranty,
                'efficiency' => $panel_efficiency,
                'logo' => $panel_logo,
                'linear_warranty' => $panel_linear_warranty,
            ],
            'inverter_specs' => [
                'model' => $inverter_model,
                'brand' => $inverter_brand,
                'power' => $inverter_power,
                'warranty' => $inverter_warranty,
                'logo' => $inverter_logo,
                'tension' => $inverter_tension,
            ],
        ];
    }
}
