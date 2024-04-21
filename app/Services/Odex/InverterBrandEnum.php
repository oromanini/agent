<?php

namespace App\Services\Odex;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SAJ_MICRO = 'SAJ Microinverter';
    case SAJ = 'Saj';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'SAJ Microinverter' => self::SAJ_MICRO,
            'Saj' => self::SAJ,
            default => Log::alert("inversor {$inverter} não encontrado!")
        };
    }
}
