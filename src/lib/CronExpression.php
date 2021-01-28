<?php

namespace lgdz\lib;

use DateTime;
use Cron\CronExpression as Cron;

class CronExpression extends InstanceClass implements InstanceInterface
{
    protected $time_zone = 'PRC';
    protected $cron_time_cn = ['分钟', '小时', '天', '月', '周'];
    protected $week_cn = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
    protected $hours = 24;
    protected $timescale = 60;

    /**
     * @param string $time_zone
     */
    public function setTimeZone(string $time_zone): void
    {
        $this->time_zone = $time_zone;
    }

    public function Cron(string $cron_exp): Cron
    {
        $cron = new Cron($cron_exp);
        $cron->isDue('now', $this->time_zone);
        return $cron;
    }

    /**
     * 获取下一次执行时间
     * @param string $cron_exp
     * @return DateTime
     * @throws \Exception
     */
    public function getNextRunDate(string $cron_exp): DateTime
    {
        return $this->Cron($cron_exp)->getNextRunDate();
    }

    public function translateToChinese(string $cron_exp)
    {
        if (!$cron_exp) {
            return 'cron表达式为空';
        }
        $string = [];
        [$minute, $hour, $day, $month, $week] = explode(' ', $cron_exp);

        if ($month !== '*')
            $string[] = sprintf('每年%s月', $month);

        if ($week !== '*')
            $string[] = sprintf('每%', $this->week_cn[$week] ?? '{周解析失败}');

        if ($day !== '*') {
            if ($day === 'L')
                $string[] = '每月最后一天';
            else
                $string[] = sprintf('每月%s号', $day);
        }

        if ($hour !== '*') {
            if (strchr($hour, '/'))
                $string[] = sprintf('每%s小时', explode('/', $hour)[1]);
            else
                $string[] = sprintf('%s时', $hour);
        }

        if ($minute !== '*') {
            if (strchr($minute, '/'))
                $string[] = sprintf('每%s分钟', explode('/', $minute)[1]);
            else
                $string[] = sprintf('%s分', $minute);
        }

        return implode(',', $string);
    }
}