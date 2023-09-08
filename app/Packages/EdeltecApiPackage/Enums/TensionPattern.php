
<?php

namespace App\Packages\EdeltecApiPackage\Enums;

enum TensionPattern: string
{
    case SINGLE_PHASE = 'Monofásico';
    case TRIPHASIC = 'Trifásico';

    case TENSION_220 = '220';
    case TENSION_380 = '380';

    public static function isCompatibleTension(string $possiblePattern, string $phase, int $tension): bool
    {
        $is220 = in_array($possiblePattern, ['Monofásico 220V', 'Bifásico 220V', 'Trifásico 220V']);

        $compatibleWithMono = $is220
            && ($phase = self::SINGLE_PHASE && $tension = 220);

        $compatibleWithTri220 =
            $is220
            && $phase == self::TRIPHASIC->value
            && $tension == 220;

        $compatibleWithTri380 =
            $possiblePattern == 'Trifásico 380V'
            && $phase == self::TRIPHASIC->value
            && $tension == 380;

        if ($compatibleWithMono || $compatibleWithTri220 || $compatibleWithTri380) {
            return true;
        }
        return false;
    }
}
