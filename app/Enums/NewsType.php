<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class NewsType extends Enum
{
    const ALL       = 1;
    const CREATOR   = 2;
    const BUYER      = 3;
    const PERSONAL  = 4;
}
