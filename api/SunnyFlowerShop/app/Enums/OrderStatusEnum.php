<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatusEnum extends Enum
{
    private const In_Progress = 0;
    private const Shipping = 1;
    private const Received = 2;

    public static function getStatusEnum() {
        return [
          "In Progress" => self::In_Progress,
          "Currently Shipping" => self::Shipping,
          "Received" => self::Received,
        ];
      }
    
      public static function getStatusAttribute($value) {
        $arr = self::getStatusEnum();
    
        return array_search($value, $arr, true);
      }
}
