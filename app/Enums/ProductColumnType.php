<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Text()
 * @method static static Number()
 * @method static static Image()
 */
final class ProductColumnType extends Enum
{
    const Text =   1;
    const Number =   2;
    const Image = 3;
}
