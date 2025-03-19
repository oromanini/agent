<?php

namespace App\Packages\EdeltecApiPackage\Enums;

use Illuminate\Support\Facades\Log;

enum PanelBrand: string
{
    case HANERSUN = 'HANERSUN TOPCON BIFACIAL';
//    case HONOR = 'HONOR';
    case OSDA = 'OSDA TOPCON BIFACIAL';
//    case SINE = 'SINE';
    case RESUN = 'RESUN TOPCON';
//    case RONMA = 'RONMA';

    public static function matchCases(string $panel): PanelBrand
    {
        return match (strtoupper($panel)) {
            'HONOR' => self::HONOR,
            'OSDA TOPCON BIFACIAL' => self::OSDA,
            'SINE' => self::SINE,
            'RESUN TOPCON' => self::RESUN,
            'RONMA' => self::RONMA,
            'HANERSUN TOPCON BIFACIAL' => self::HANERSUN,
            default => throw new \InvalidArgumentException("Panel brand '$panel' is not recognized."),
        };
    }
}
