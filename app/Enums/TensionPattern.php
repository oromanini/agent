<?php

namespace App\Enums;

enum TensionPattern
{
    const MONOFASICO_220V = 1;
    const BIFASICO_220V = 2;
    const TRIFASICO_220V = 3;
    const TRIFASICO_380V = 4;

    public static function setTensionPossibilities(int $tension): array
    {
        $compatible_with_220v = [TensionPattern::MONOFASICO_220V, TensionPattern::TRIFASICO_220V];
        $compatible_with_380v = [TensionPattern::TRIFASICO_380V];

        return match ($tension) {
            TensionPattern::MONOFASICO_220V, TensionPattern::BIFASICO_220V, TensionPattern::TRIFASICO_220V  => $compatible_with_220v,
            TensionPattern::TRIFASICO_380V  => $compatible_with_380v,
        };
    }

    public static function translateExternalTension(string $tensionPattern): int
    {
        return match ($tensionPattern) {
            'Monofásico 220', 'MONO-220', 'MONOFÁSICO-220v', 'MONOFASICO-220V', 'BIF-220', 'BIFÁSICO-220v' => self::MONOFASICO_220V,
            'Trifásico 220', 'TRI-220', 'TRIFASICO-220v', 'TRIFASICO-220V' => self::TRIFASICO_220V,
            'Trifásico 380', 'TRI-380', 'TRIFASICO-380v', 'TRIFASICO-380V' => self::TRIFASICO_380V,
            default => 0
        };
    }
}
