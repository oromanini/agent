<?php

namespace App\Enums;

use Illuminate\Support\Facades\Log;

enum TensionPattern
{
    const mono220 = 1;
    const tri220 = 2;
    const tri380 = 3;

    public static function setTensionPattern(string $tensionPattern): int
    {
        return match ($tensionPattern) {
            'Monofásico 220', 'MONO-220', 'MONOFÁSICO-220v', 'MONOFASICO-220V', 'BIF-220', 'BIFÁSICO-220v' => self::mono220,
            'Trifásico 220', 'TRI-220', 'TRIFASICO-220v', 'TRIFASICO-220V' => self::tri220,
            'Trifásico 380', 'TRI-380', 'TRIFASICO-380v', 'TRIFASICO-380V' => self::tri380,
            default => 0
        };
    }
}
