<?php

namespace App\Services\Odex;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SAJ_MICRO = 'SAJ Microinverter';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'SAJ Microinverter' => self::SAJ_MICRO,
            default => Log::alert("inversor {$inverter} não encontrado!")
        };
    }
}
