<?php

namespace App\Enums;

enum TensionPattern: int
{
    case MONOFASICO_220V = 1;
    case BIFASICO_220V = 2;
    case TRIFASICO_220V = 3;
    case TRIFASICO_380V = 4;
    case MONOFASICO_380V = 5;

    public static function setTensionPossibilities(int $tension): array
    {
        $compatible_with_220v = [TensionPattern::MONOFASICO_220V->value, TensionPattern::TRIFASICO_220V->value];
        $compatible_with_380v = [TensionPattern::TRIFASICO_380V->value];

        return match ($tension) {
            self::MONOFASICO_220V->value,
            self::BIFASICO_220V->value,
            self::TRIFASICO_220V->value  => $compatible_with_220v,

            self::TRIFASICO_380V->value  => $compatible_with_380v,
            default => throw new \Exception('Tensão não encontrada'),
        };
    }

    public static function translateExternalTension(string $tensionPattern): int
    {
        return match ($tensionPattern) {
            'Monofásico 220', 'MONO-220', 'MONOFÁSICO-220v', 'MONOFASICO-220V', 'BIF-220', 'BIFÁSICO-220v' => self::MONOFASICO_220V->value,
            'Trifásico 220', 'TRI-220', 'TRIFASICO-220v', 'TRIFASICO-220V' => self::TRIFASICO_220V->value,
            'Trifásico 380', 'TRI-380', 'TRIFASICO-380v', 'TRIFASICO-380V' => self::TRIFASICO_380V->value,
            'Monofásico 380'=> self::MONOFASICO_380V->value,
            default => 0
        };
    }
}
