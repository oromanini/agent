<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TensionPattern extends Enum
{
    const mono220 = 'Monofásico 220V';
    const bi220 =   'Bifásico 220V';
    const tri220 = 'Trifásico 220V';
    const tri380 = 'Trifásico 380';
}
