<?php

namespace App\Services\Fotus;

use Illuminate\Support\Facades\Log;

enum InverterBrandEnum: string
{
    case SOFAR = 'Sofar';
    case SOLPLANET = 'Solplanet';
    case SOLIS = 'Solis';

    public static function matchCases(string $inverter): self
    {
        return match (strtoupper($inverter)) {
            'Sofar' => self::SOFAR,
            'Solplanet' => self::SOLPLANET,
            'Solis' => self::SOLIS,
            default => Log::alert("inversor {$inverter} não encontrado!")
        };
    }
}
