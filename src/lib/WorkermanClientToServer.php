<?php

declare (strict_types=1);

namespace lgdz\lib;

use Closure;
use Exception;

class WorkermanClientToServer extends InstanceClass implements InstanceInterface
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param array $input
     * @throws Exception
     */
    protected function verify(array $input)
    {
        if (!$this->factory->DataAuth($this->secret)->verify($input)) {
            throw new Exception('验证签名错误');
        }
    }

    /**
     * @param array $input
     * @param Closure $callback ($input)
     * @return mixed
     * @throws Exception
     */
    public function onConnect(array $input, \Closure $callback)
    {
        $this->verify($input);
        return $this->factory->HttpResponse()->ok($callback($input));
    }

    /**
     * @param array $input
     * @param Closure $callback (string $method,...$args)
     * @return string
     * @throws Exception
     */
    public function onMessage(array $input, \Closure $callback)
    {
        $this->verify($input);
        /*
             * 可用参数
             * appid 应用唯一标识(暂无)
             * timestamp 请求时间戳
             * client_id websocket分配的唯一标识
             * uid string 系统内用户唯一标识
             * sign string 签名
             * body array 业务参数
             * - method string 方法
             * - option array 参数
             */
        // 业务处理
        // ...
        try {
            $message = $input['body']['message'];
            $method  = $message['method'] ?? null;
            $option  = $message['option'] ?? [];
            if (is_null($method)) {
                throw new Exception('method undefined');
            }
            return $this->factory->HttpResponse()->ok($callback($method, ...array_values($option)));
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param array $input
     * @param Closure $callback ($input)
     * @return string
     * @throws Exception
     */
    public function onClose(array $input, \Closure $callback)
    {
        $this->verify($input);
        /*
             * 可用参数
             * appid 应用唯一标识
             * timestamp 请求时间戳
             * client_id websocket分配的唯一标识
             * uid string 系统内用户唯一标识
             * sign string 签名
             */
        return $this->factory->HttpResponse()->ok($callback($input));
    }
}