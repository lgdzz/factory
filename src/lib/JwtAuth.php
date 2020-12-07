<?php

declare (strict_types=1);

namespace lgdz\lib;

use Firebase\JWT\{JWT, ExpiredException, SignatureInvalidException};
use Redis;
use Exception;

class JwtAuth extends InstanceClass implements InstanceInterface
{
    // 密钥
    private $secret = '';
    // 用户登录凭证缓存KEY
    private $ticket_key = 'user_ticket';
    /**
     * redis操作实例
     * @var Redis
     */
    private $redis;

    /**
     * JwtAuth constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['secret'])) $this->secret = $config['secret'];
        if (isset($config['ticket_key'])) $this->ticket_key = $config['ticket_key'];
        if (isset($config['redis'])) $this->redis = $config['redis'];
    }

    /**
     * 签发（默认缓存1天）
     * @param int $uid 用户ID
     * @param array $body 附加额外数据
     * @param int $expire 过期时间（秒）
     * @return array
     */
    public function issue(int $uid, array $body = [], int $expire = 86400): array
    {
        $nowTime = time();
        // 过期时间
        $timestamp = $nowTime + $expire;
        $payload   = [
            // 签发时间
            'iat'  => $nowTime,
            'exp'  => $timestamp,
            'body' => array_merge(['uid' => $uid, 'ticket' => $this->factory->Helper()->randomString(50)], $body)
        ];
        $this->redis->hSet($this->ticket_key, (string)$uid, serialize($payload));
        return [JWT::encode($payload, $this->secret), date('Y-m-d H:i:s', $timestamp)];
    }

    /**
     * 验证
     * @param string $Authorization
     * @param bool $checkTicket
     * @return mixed
     * @throws Exception
     */
    public function check(string $Authorization, bool $checkTicket = false)
    {
        try {
            $decoded = JWT::decode($Authorization, $this->secret, array('HS256'));
        } catch (SignatureInvalidException $e) {
            throw new Exception('签名失败');
        } catch (ExpiredException $e) {
            throw new Exception('登录凭证过期');
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        // ticket验证
        if ($checkTicket) {
            $uid     = $decoded->body->uid;
            $payload = $this->redis->hGet($this->ticket_key, (string)$uid);
            if (!$payload) {
                throw new Exception('ticket失效，请重新登录');
            }
            $payload = unserialize($payload);
            if ($payload['body']['ticket'] !== $decoded->body->ticket) {
                throw new Exception('ticket失效，请重新登录');
            } elseif ($decoded->exp < time()) {
                throw new Exception('ticket过期，请重新登录');
            }
        }
        return $decoded->body;
    }
}