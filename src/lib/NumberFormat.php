<?php

namespace lgdz\lib;

/**
 * Class NumberFormat 数字格式化
 * @package lgdz\lib
 */
class NumberFormat extends InstanceClass implements InstanceInterface
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
}