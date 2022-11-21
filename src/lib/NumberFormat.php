<?php

namespace lgdz\lib;

/**
 * Class NumberFormat 数字格式化
 * @package lgdz\lib
 */
class NumberFormat
{
    public function toString($number, int $decimal = 2): string
    {
        return sprintf("%.{$decimal}f", $number);
    }

    public function toInt($number): int
    {
        return (int)$number;
    }

    public function toFloat($number): float
    {
        return (float)$number;
    }

    public function toMoney($value, int $decimal = 2): string
    {
        return $this->toString($this->toFloat($value), $decimal);
    }

    // 将小数点后面的0舍弃
    public function discardZero($value): string
    {
        return rtrim(rtrim($value, '0'), '.');
    }
}