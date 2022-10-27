<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatusEnum extends Enum
{
  private const Pending = 0;
  private const Completed = 1;

  public static function getStatusEnum()
  {
    return [
      "Pending" => self::Pending,
      "Completed" => self::Completed,
    ];
  }

  public static function getStatusAttribute($value)
  {
    $arr = self::getStatusEnum();

    return array_search($value, $arr, true);
  }
}
