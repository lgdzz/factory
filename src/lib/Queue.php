<?php

namespace lgdz\lib;

use Cron\CronExpression;
use Curl\Curl;
use DateTime;

class Queue extends InstanceClass implements InstanceInterface
{
    // 应用ID
    protected $appid = '';
    // 任务执行地址
    protected $callback = [
        'host' => '',
        'port' => 80,
        'path' => '/'
    ];
    // 生产者接口地址
    protected $producer_url = '';

    public function __construct(array $config = [])
    {
        isset($config['producer_url']) && $this->producer_url = $config['producer_url'];
        isset($config['appid']) && $this->appid = $config['appid'];
        if (isset($config['callback_url'])) {
            $callback       = parse_url($config['callback_url']);
            $scheme         = isset($callback['scheme']) ? $callback['scheme'] . '//' : '';
            $host           = $callback['host'];
            $port           = $callback['port'] ?? 80;
            $path           = $callback['path'] ?? '/';
            $this->callback = [
                'host' => $scheme . $host,
                'port' => $port,
                'path' => $path
            ];
        }
    }

    protected function package(array $input)
    {
        return array_merge(
            [
                'appid'    => $this->appid,
                'callback' => $this->callback
            ],
            $input
        );
    }

    protected function request(array $input)
    {
        return $this->factory->HttpRequest()->post($this->producer_url, $this->package($input), function (Curl $curl) {
            $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
        });
    }

    /**
     * 立即执行
     * @param array $input
     * @return mixed|null
     */
    public function push(array $input)
    {
        return $this->request($input);
    }

    /**
     * 延时执行
     * @param array $input
     * @param DateTime|string|int $time
     * @return mixed|null
     */
    public function delayed(array $input, $time)
    {
        if ($time instanceof DateTime) {
            $delay = $time->getTimestamp();
        } elseif (is_string($time)) {
            $delay = (int)strtotime($time);
        } else {
            $delay = (int)$time;
        }
        return $this->request(array_merge(
            $input,
            [
                'delay' => $delay
            ]
        ));
    }

    /**
     * 周期执行
     * @param array $input
     * @param string $key
     * @param string $command
     * @param string $crontab
     * @return mixed|null
     * @throws \Exception
     */
    public function cycle(array $input, string $key, string $command, string $crontab)
    {
        return $this->request(array_merge(
            $input,
            [
                'key'     => $key,
                'command' => $command,
                'cycle'   => $crontab,
                'delay'   => $this->next($crontab)
            ]
        ));
    }

    /**
     * @param string $rule
     * rule 第1个*：分钟 (0 - 59)
     * rule 第2个*：小时 (0 - 23)
     * rule 第3个*：一个月中的第几天 (1 - 31)
     * rule 第4个*：月份 (1 - 12)
     * rule 第5个*：星期中星期几 (0 - 6) (星期天 为0)
     * @return int
     * @throws \Exception
     */
    protected function next(string $rule)
    {
        $cron = new CronExpression($rule);
        $cron->isDue('now', 'PRC');
        return $cron->getNextRunDate()->getTimestamp();
    }
}