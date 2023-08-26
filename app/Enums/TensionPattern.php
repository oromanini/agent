<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

enum TensionPattern
{
    const mono220 = 'Monofásico 220V';
    const bi220 =   'Bifásico 220V';
    const tri220 = 'Trifásico 220V';
    const tri380 = 'Trifásico 380V';

    public static function setTensionPattern(string $tensionPattern): string
    {
        return match ($tensionPattern) {
            'MONO-220', 'MONOFÁSICO-220v', 'MONOFASICO-220V', 'BIF-220', 'BIFÁSICO-220v' => self::mono220,
            'TRI-220', 'TRIFASICO-220v', 'TRIFASICO-220V' => self::tri220,
            'TRI-380', 'TRIFASICO-380v', 'TRIFASICO-380V' => self::tri380,
        };
    }
}
