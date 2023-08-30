<?php

namespace App\Packages\EdeltecApiPackage\Enums;

enum PanelBrand: string
{
    case HONOR = 'HONOR';
    case OSDA = 'OSDA';
    case SINE = 'SINE';
    case RESUN = 'RESUN';

    public static function matchCases(string $panel): self
    {
        return match (strtoupper($panel)) {
            'HONOR' => self::HONOR,
            'OSDA' => self::OSDA,
            'SINE' => self::SINE,
            'RESUN' => self::RESUN,
            default => dump($panel)
        };
    }
}
