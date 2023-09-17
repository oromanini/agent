<?php

namespace App\Packages\EdeltecApiPackage\Enums;

enum InverterImage: string
{
    const PREFIX = '/EdeltecApiPackage/img/inverter_picture';

    case SAJ = self::PREFIX . '/saj.png';
    case GROWATT = self::PREFIX . '/growatt.png';
    case SUNGROW = self::PREFIX . '/sungrow.png';
    case DEYE = self::PREFIX . '/deye-string.png';

    public static function getByCase(string $brand): ?string
    {
        foreach (self::cases() as $case) {
            if ($brand == $case->name) {
                return $case->value;
            }
        }
        return null;
    }
}
