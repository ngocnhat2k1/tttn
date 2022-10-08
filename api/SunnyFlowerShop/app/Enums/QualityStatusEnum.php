<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class QualityStatusEnum extends Enum
{
    private const Good = 5;
    private const Great = 4;
    private const Neutral = 3;
    private const Bad = 2;
    private const Worst = 1;

    public static function getQualityEnum()
    {
        return [
            "Good" => self::Good,
            "Great" => self::Great,
            "Neutral" => self::Neutral,
            "Bad" => self::Bad,
            "Worst" => self::Worst,
        ];
    }

    public static function getQualityAttribute($value)
    {
        $arr = self::getQualityEnum();

        return array_search($value, $arr, true);
    }
}
