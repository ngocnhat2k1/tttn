<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ShowValueEnum extends Enum
{
    private const Hide = 0;
    private const Show = 1;
    
    public static function getShowValueEnum()
    {
        return [
            "Không" => self::Hide,
            "Có" => self::Show
        ];
    }

    public static function getShowValueAttribute($value)
    {
        $arr = self::getShowValueEnum();

        return array_search($value, $arr, true);
    }
}
