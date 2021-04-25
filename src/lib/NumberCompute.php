<?php

namespace lgdz\lib;

/**
 * Class NumberCompute 数字计算
 * @package lgdz\lib
 */
class NumberCompute extends InstanceClass implements InstanceInterface
{
    /**
     * 除法
     * @param int|float|string $dividend 除数
     * @param int|float|string $divisor 被除数
     * @param int $decimal 小数点
     * @return string
     */
    public function bcdiv($dividend, $divisor, int $decimal = 2): string
    {
        if (!$divisor) {
            return '0.00';
        }
        return (string)bcdiv($dividend, $divisor, $decimal);
    }

    public function percent($value): int
    {
        return intval($value * 100);
    }

    public function percentToString($value): string
    {
        return sprintf('%d%%', $this->percent($value));
    }
}