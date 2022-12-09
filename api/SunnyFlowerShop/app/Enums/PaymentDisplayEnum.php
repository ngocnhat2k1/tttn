<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PaymentDisplayEnum extends Enum
{
  private const CashSettlement = 0;
  private const MomoATM = 1;
  private const MomoQR = 2;

  public static function getStatusEnum()
  {
    return [
      "Thanh toán khi nhận hàng." => self::CashSettlement,
      "Thanh toán qua MOMO ATM." => self::MomoATM,
      "Thanh toán qua MOMO QR." => self::MomoQR
    ];
  }

  public static function getPaymentDisplayAttribute($value)
  {
    $arr = self::getStatusEnum();

    return array_search($value, $arr, true);
  }
}
