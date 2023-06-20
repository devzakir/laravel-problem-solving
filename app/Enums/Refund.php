<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Refund extends Enum
{
    const FINISH   =   2;
    const WAIT     =   1;
}
