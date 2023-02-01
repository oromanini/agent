<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class RoofStructure extends Enum
{
    const Colonial =   1;
    const Trapezoidal =   2;
    const Laje = 3;
    const ParafMetal = 4;
    const ParafMadeira = 5;
    const Solo = 6;
    const Ondulada = 7;
}
