<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class InverterBrands extends Enum
{
    const Growatt =   1;
    const Chint =   2;
    const Deye = 3;
}
