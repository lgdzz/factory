<?php

namespace lgdz\lib;

/**
 * Class NumberCompute 数字计算
 * @package lgdz\lib
 */
class NumberCompute
{
    /**
     * 除法
     * @param int|float|string $dividend 除数
     * @param int|float|string $divisor 被除数
     * @param int $decimal 小数点
     * @return float
     */
    public function bcdiv($dividend, $divisor, int $decimal = 2): float
    {
        if (!$divisor) {
            return 0;
        }
        return bcdiv($dividend, $divisor, $decimal);
    }

    /**
     * 计算百分比，结果带百分号
     * @param $dividend
     * @param $divisor
     * @param int $decimal
     * @return string
     */
    public function rate($dividend, $divisor, int $decimal = 2)
    {
        return $this->percentToString($this->bcdiv($dividend, $divisor, $decimal));
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