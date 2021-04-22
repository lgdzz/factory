<?php

declare (strict_types=1);

namespace lgdz\lib;

class Time extends InstanceClass implements InstanceInterface
{
    /**
     * 获取指定日期范围内的所有日期
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function getDatesBetweenDays(string $start_date, string $end_date)
    {
        $dates = [];
        if (strtotime($start_date) > strtotime($end_date)) {
            // 如果开始日期大于结束日期，直接return 防止下面的循环出现死循环
            return $dates;
        } elseif ($start_date == $end_date) {
            // 开始日期与结束日期是同一天时
            array_push($dates, $start_date);
            return $dates;
        } else {
            array_push($dates, $start_date);
            $current_date = $start_date;
            do {
                $next_date = date('Y-m-d', strtotime($current_date . ' +1 days'));
                array_push($dates, $next_date);
                $current_date = $next_date;
            } while ($end_date != $current_date);

            return $dates;
        }
    }

    /**
     * @param string|array $value
     * @return array
     */
    public function dateBetween($value): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (is_array($value)) {
            [$start, $end] = $value;
            $value[0] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($start))));
            $value[1] = date('Y-m-d H:i:s', strtotime('+1 day', strtotime(date('Y-m-d', strtotime($end)))) - 1);
        }

        return $value;
    }

    /**
     * @param string|array $value
     * @return array
     */
    public function dateBetweenTimestamp($value): array
    {
        [$start, $end] = $this->dateBetween($value);
        return [strtotime($start), strtotime($end)];
    }

    public function todayTimestampBetween(string $date)
    {
        [$start, $end] = $this->dateBetween([$date, $date]);
        return [strtotime($start), strtotime($end)];
    }

    public function computeTime(int $start, int $end): int
    {
        return abs($start - $end);
    }

    // 计算两个时间差值并格式化为字符串
    public function computeTimeFormat(int $start, int $end): string
    {
        $duration = $this->computeTime($start, $end);
        $output = '';
        foreach ([31536000 => '年', 86400 => '天', 3600 => '小时', 60 => '分', 1 => '秒'] as $key => $value) {
            if ($duration >= $key) $output .= floor($duration / $key) . $value;
            $duration %= $key;
        }
        if ($output == '') {
            $output = 0;
        }
        return $output;
    }
}