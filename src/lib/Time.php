<?php

declare (strict_types=1);

namespace lgdz\lib;

use Exception;
use Moment\Moment;
use Moment\MomentException;

class Time
{
    private $time_zone = 'Asia/Shanghai';

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

    public function todayBetween(string $date)
    {
        return $this->dateBetween([$date, $date]);
    }

    /**
     * 获取指定日期时间范围
     * @param string $date 格式：Y-m-d|Y-m-d H:i:s
     * @param bool $is_int
     * @return array[string|int]
     */
    public function dayBetween(string $date, bool $is_int = false)
    {
        return $is_int ? $this->dateBetweenTimestamp([$date, $date]) : $this->dateBetween([$date, $date]);
    }

    /**
     * 获取指定月份时间范围
     * @param string $month 格式：Y-m
     * @param bool $is_int
     * @return array[string|int]
     */
    public function monthBetween(string $month, bool $is_int = false)
    {
        $month = date('Y-m', strtotime($month));
        $start = strtotime($month);
        $end = strtotime('+1 month', $start) - 1;
        if (!$is_int) {
            $start = date('Y-m-d H:i:s', $start);
            $end = date('Y-m-d H:i:s', $end);
        }
        return [$start, $end];
    }

    /**
     * 获取指定年份时间范围
     * @param string $year 格式：Y
     * @param bool $is_int
     * @return array[string|int]
     */
    public function yearBetween(string $year, bool $is_int = false)
    {
        $year = date('Y', strtotime($year . '-01'));
        $start = strtotime($year . '-01');
        $end = strtotime('+1 year', $start) - 1;
        if (!$is_int) {
            $start = date('Y-m-d H:i:s', $start);
            $end = date('Y-m-d H:i:s', $end);
        }
        return [$start, $end];
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

    // 获取一天内hours
    public function getHours(string $day = 'now')
    {
        $hours = [];
        if ($day === 'now' || strtolower($day) >= strtolower(date('Y-m-d'))) {
            $date = new \DateTime('now', new \DateTimeZone($this->time_zone));
            $last_hour = $date->format('H');
        } else {
            $last_hour = 23;
        }
        for ($i = 0; $i <= $last_hour; $i++) {
            array_push($hours, str_pad((string)$i, 2, '0', STR_PAD_LEFT));
        }
        return $hours;
    }

    // 获取一个月内days
    public function getDays(string $month = 'now')
    {
        $days = [];
        $now_month = date('Y-m');
        if ($month === 'now' || strtolower($month) >= strtolower($now_month)) {
            $month = $now_month;
            $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
            $last_day = $date->format('d');
        } else {
            $date = new \DateTime($month, new \DateTimeZone($this->time_zone));
            $last_day = date('d', strtotime('+1 month', strtotime($date->format('Y-m-d'))) - 1);
        }
        for ($i = 1; $i <= $last_day; $i++) {
            array_push($days, $month . '-' . str_pad((string)$i, 2, (string)0, STR_PAD_LEFT));
        }
        return $days;
    }

    // 获取一年内months
    public function getMonths(string $year = 'now')
    {
        $months = [];
        $now_year = date('Y');
        if ($year === 'now' || strtolower($year) >= strtolower($now_year)) {
            $year = $now_year;
            $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
            $last_month = $date->format('m');
        } else {
            $date = new \DateTime($year, new \DateTimeZone($this->time_zone));
            $last_month = 12;
        }
        for ($i = 1; $i <= $last_month; $i++) {
            array_push($months, $year . '-' . str_pad((string)$i, 2, (string)0, STR_PAD_LEFT));
        }
        return $months;
    }

    // 日期计算出年龄
    public function computeAge(string $date)
    {
        return (int)(date('Y') - substr($date, 0, 4));
    }

    /**
     * @param string $dateTime
     * @return Moment
     * @throws Exception
     */
    public function moment(string $dateTime)
    {
        try {
            Moment::setLocale('zh_CN');
            return new Moment($dateTime, $this->time_zone);
        } catch (MomentException $e) {
            throw new Exception('MomentException:' . $e->getMessage());
        }
    }

    // 易读时间
    public function easyReadString(string $dateTime)
    {
        try {
            return $this->moment($dateTime)->fromNow()->getRelative();
        } catch (MomentException $e) {
            throw new Exception('MomentException:' . $e->getMessage());
        }
    }

    // 当前时间
    public function nowTime()
    {
        $time = time();
        $week = date('w', $time);
        $weekName = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        return [
            'timestamp'  => $time,
            'datetime_1' => date('Y-m-d H:i:s', $time),
            'datetime_2' => date('Y年m月d日 H时i分s秒', $time),
            'week_1'     => $week,
            'week_2'     => $weekName[(int)$week],
        ];
    }

    /**
     * 日期适配器
     * @param string|int $dateTime
     * @return bool|false|string
     */
    public function dateAdapter($dateTime)
    {
        $len = strlen($dateTime);
        // 1开头 且 长度为10位或13位 当时间戳处理
        if (substr($dateTime, 0, 1) === '1' && ($len === 10 || $len === 13)) {
            return $len === 10 ? date('Y-m-d', $dateTime) : date('Y-m-d', $dateTime / 1000);
        } else {
            preg_match('/(20\d{2}).*?(\d{1,2}).*?(\d{1,2})/', trim($dateTime), $result);
            $year = $result[1] ?? null;
            $month = $result[2] ?? null;
            $day = $result[3] ?? null;
            if (is_null($year) || is_null($month) || is_null($day) || $month < 1 || $month > 12 || $day < 1 || $day > 31) {
                return false;
            }
            return date('Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day, (int)$year));
        }
    }
}