<?php

namespace App\Packages\EdeltecApiPackage\Enums;

use Illuminate\Support\Facades\Log;

enum PanelBrand: string
{
    case HONOR = 'HONOR';
    case OSDA = 'OSDA TOPCON BIFACIAL';
    case SINE = 'SINE';
    case RESUN = 'RESUN TOPCON';
    case RONMA = 'RONMA';
    case HANERSUN = 'HANERSUN TOPCON BIFACIAL';

    public static function matchCases(string $panel): self
    {
        return match (strtoupper($panel)) {
            'HONOR' => self::HONOR,
            'OSDA' => self::OSDA,
            'SINE' => self::SINE,
            'RESUN' => self::RESUN,
            'RONMA' => self::RONMA,
            'HANERSUN' => self::HANERSUN,
            default => Log::alert("Padrão {$panel} não encontrado!")
        };
    }
}
