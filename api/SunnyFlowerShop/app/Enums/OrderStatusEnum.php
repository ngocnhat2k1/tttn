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
  private const processing = -2;
  private const cancel = -1;
  private const pending = 0;
  private const payment = 1;
  private const ready_to_pick = 2;
  private const picked = 3;
  private const delivering = 4;
  private const delivered = 5;
  private const completed = 6;

  public static function getStatusEnum()
  {
    return [
      "Đơn hàng đang được cửa hàng xử lý." => self::processing,
      "Đơn hàng đang chờ xử lý." => self::pending,
      "Đơn hàng đang chờ thanh toán." => self::payment,
      "Đơn hàng đang gửi cho đơn vị vận chuyển." => self::ready_to_pick,
      "Đơn vị vận chuyển đã nhận đơn hàng." => self::picked,
      "Đơn hàng đang được giao." => self::delivering,
      "Đơn hàng được giao thành công." => self::delivered,
      "Đã nhận hàng." => self::completed,
      "Đơn hàng đã bị hủy" => self::cancel,
    ];
  }

  public static function getStatusEnumReverse()
  {
    return [
      "-2" => "processing",
      "0" => "pending",
      "1" => "payment",
      "2" => "ready_to_pick",
      "3" => "picked",
      "4" => "delivering",
      "5" => "delivered",
      "6" => "completed",
      "-1" => "cancel",
    ];
  }

  public static function getStatusAttribute($value)
  {
    $arr = self::getStatusEnum();

    return array_search($value, $arr, true);
  }

  public static function getStatusAttributeReverse($value)
  {
    $arr = self::getStatusEnumReverse();

    return array_search($value, $arr, true);
  }
}
