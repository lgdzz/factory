<?php

namespace lgdz\lib;

use Exception;
use Swoole\Coroutine\Redis;
use Swoole\Coroutine\Http\Client;

class QueueService extends InstanceClass implements InstanceInterface
{
    protected $config = [
        'redis_ip'      => '100.31.0.3',
        'redis_port'    => 6379,
        'producer_ip'   => '100.31.0.4',
        'producer_port' => 9501,
        'api_ip'        => '100.31.0.51',
        'api_port'      => 9501
    ];

    public function getRedisIp(): string
    {
        return $this->config['redis_ip'];
    }

    public function getRedisPort(): int
    {
        return $this->config['redis_port'];
    }

    public function getApiIp(): string
    {
        return $this->config['api_ip'];
    }

    public function getApiPort(): int
    {
        return $this->config['api_port'];
    }

    public function getProducerIp(): string
    {
        return $this->config['producer_ip'];
    }

    public function getProducerPort(): string
    {
        return $this->config['producer_port'];
    }

    /**
     * @var Redis
     */
    protected $redis;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        // 初始化redis连接
        $redis = new Redis();
        $redis->connect($this->getRedisIp(), $this->getRedisPort());
    }

    protected function print_error_log($msg)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $time = date('Y-m-d H:i:s');
        echo "[{$time}] 任务执行失败，30秒后重试：{$msg}\n";
    }

    protected function execute_cycle_task_before(array $data)
    {
        $unique_key = (md5(($data['appid'] ?? '') . ($data['key'] ?? '')));
        // 口令
        $command = $this->redis->hGet('cycle_run_status', $unique_key);
        return $command === $data['command'];
    }

    protected function update_cycle_task_total(string $cron_key, float $runtime, bool $result)
    {
        $client = new Client($this->getApiIp(), $this->getApiPort());
        $client->post('/crontab_run_change_total', ['key' => $cron_key, 'runtime' => $runtime]);
        $client->close();
    }

    protected function create_next_cycle_task(array $data)
    {
        $data['delay'] = $this->factory->CronExpression()->getNextRunDate($data['cycle'])->getTimestamp();
        if (isset($data['attempt'])) {
            unset($data['attempt']);
        }
        $this->producer($data);
    }

    protected function attempt(array $data)
    {
        $attempt = $data['attempt'] ?? 0;
        $attempt++;

        $data['attempt'] = $attempt;
        $data['delay']   = time() + 30;
        $this->producer($data);
    }

    /**
     * 执行
     * @param array $data
     * @return array
     */
    protected function execute(array $data)
    {
        $start    = microtime(true);
        $callback = $data['callback'];
        $client   = new Client($callback['host'], $callback['port']);
        $client->set(['timeout' => $callback['timeout'] ?? 10]);
        $client->post($callback['path'], $data);
        $response = $client->body;
        $client->close();
        $end     = microtime(true);
        $runtime = round($end - $start, 3);
        if ($response === 'Success') {
            $result = true;
        } else {
            // 30秒后重试
            $this->attempt($data);
            $result = false;
        }
        return ['result' => $result, 'runtime' => $runtime];
    }

    protected function execute_task(array $data)
    {
        $this->execute($data);
    }

    protected function execute_cycle_task(array $data)
    {
        // 口令不正确，跳过任务
        if (!$this->execute_cycle_task_before($data))
            return;
        // 执行任务
        $result = $this->execute($data);
        // 更新执行时间和次数
        $this->update_cycle_task_total($data['key'], $result['runtime'], $result['result']);
        // 执行任务成功，创建下一次任务
        if ($result['result'])
            $this->create_next_cycle_task($data);
    }

    protected function execute_cycle_task_test(array $data)
    {
        $result = $this->execute($data);
        // 更新执行时间和次数
        $this->update_cycle_task_total($data['key'], $result['runtime'], $result['result']);
    }

    /**
     * 生产
     * @param array $data
     */
    protected function producer(array $data)
    {
        try {
            $client = new Client($this->getProducerIp(), $this->getProducerPort());
            $client->post('/', $data);
            $response = $client->body;
            $client->close();
            if ($response !== 'Success') {
                throw new Exception("重新发布生成任务失败;{$response}");
            }
        } catch (\Throwable $e) {
            $this->print_error_log($e->getMessage());
            $this->producer($data);
        }
    }

    /**
     * 消费
     * @param array $data
     */
    public function consumer(array $data)
    {
        Co\run(function () use ($data) {
            try {
                $appid    = $data['appid'] ?? null;
                $callback = $data['callback'] ?? null;
                if (!is_null($appid) && !is_null($callback)) {
                    $cycle = $data['cycle'] ?? null;
                    if ($cycle) {
                        if ($cycle === 'test') {
                            // 执行测试周期任务
                            $this->execute_cycle_task_test($data);
                        } else {
                            // 执行周期任务
                            $this->execute_cycle_task($data);
                        }
                    } else {
                        // 执行普通任务
                        $this->execute_task($data);
                    }
                }
            } catch (\Throwable $e) {
                $this->print_error_log($e->getMessage());
                $this->attempt($data);
            }
        });
    }
}