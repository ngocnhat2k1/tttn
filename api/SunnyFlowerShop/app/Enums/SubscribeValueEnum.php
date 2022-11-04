<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SubscribeValueEnum extends Enum
{
    private const NonSubscribe = 0;
    private const Subscribed = 1;
    
    public static function getSubscribeValueEnum()
    {
        return [
            "Non Subscribe" => self::NonSubscribe,
            "Subscribed" => self::Subscribed
        ];
    }

    public static function getSubscribeValueAttribute($value)
    {
        $arr = self::getSubscribeValueEnum();

        return array_search($value, $arr, true);
    }
}
