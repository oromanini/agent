<?php

namespace App\Services\Fotus;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SOLPLANET = 'SOLPLANET';
    case SOFAR = 'SOFAR';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'Solplanet' => self::SOLPLANET,
            'Sofar' => self::SOFAR,
            default => Log::info("inversor {$inverter} não encontrado!")
        };
    }
}
