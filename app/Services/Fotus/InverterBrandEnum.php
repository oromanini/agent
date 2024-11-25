<?php

namespace App\Services\Fotus;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SOLPLANET = 'SOLPLANET';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'Solplanet' => self::SOLPLANET,
            default => Log::info("inversor {$inverter} não encontrado!")
        };
    }
}
