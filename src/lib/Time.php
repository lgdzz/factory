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

    public function dateBetween($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (is_array($value)) {
            $value[0] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($value[0]))));
            $value[1] = date('Y-m-d H:i:s', strtotime('+1 day', strtotime(date('Y-m-d', strtotime($value[1])))) - 1);
        }

        return $value;
    }

    public function dateBetweenTimestamp($value)
    {
        [$start, $end] = $this->dateBetween($value);
        return [strtotime($start), strtotime($end)];
    }

    public function todayTimestampBetween(string $date)
    {
        [$start, $end] = $this->dateBetween([$date, $date]);
        return [strtotime($start), strtotime($end)];
    }
}