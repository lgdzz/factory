<?php

declare (strict_types=1);

namespace lgdz\lib;

use Closure;
use Exception;
use lgdz\lib\workerman\SendToUser;

class WorkermanServerToClient extends InstanceClass implements InstanceInterface
{
    use SendToUser;

    private $appid;
    private $secret;
    private $gateway;
    private $http;

    /**
     * WorkermanServerToClient constructor.
     * @param $appid
     * @param $secret
     * @param $gateway
     */
    public function __construct(string $appid, string $secret, string $gateway)
    {
        $this->appid   = $appid;
        $this->secret  = $secret;
        $this->gateway = $gateway;
    }

    protected function sign(array &$data)
    {
        $data['sign'] = $this->factory->DataAuth($this->secret)->sign($data);
    }

    protected function verify(array $data): bool
    {
        return $this->factory->DataAuth($this->secret)->verify($data);
    }

    /**
     * @param string $method http://doc2.workerman.net/lib-gateway-functions.html
     * @param array $body
     * @param Closure $callback (array $dataset)
     * @return mixed
     * @throws Exception
     */
    public function send(string $method, array $body, Closure $callback = null)
    {
        $this->sign($body);
        $response = $this->factory->HttpRequest()->post($this->gateway, ['method' => $method, 'body' => $body]);
        if (!isset($response->error)) {
            throw new Exception('接口请求失败');
        } elseif ($response->error !== 0) {
            throw new Exception($response->message);
        } else {
            if (!is_null($callback)) {
                return $callback($response);
            }
        }
    }

    /**
     * @param array $input
     * @param Closure $callback
     * @return mixed
     * @throws Exception
     */
    public function receive(array $input, Closure $callback)
    {
        if ($this->verify($input)) {
            // 业务处理
            return $callback($input);
        } else {
            throw new Exception('验证签名失败');
        }
    }
}