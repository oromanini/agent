<?php

namespace App\Services\Odex;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SAJ_MICRO = 'SAJ Microinverter';
    case SAJ = 'SAJ';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'SAJ Microinverter' => self::SAJ_MICRO,
            'SAJ' => self::SAJ,
            default => Log::alert("inversor {$inverter} não encontrado!")
        };
    }
}
