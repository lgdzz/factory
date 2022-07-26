<?php

declare (strict_types=1);

namespace lgdz\lib;

use Firebase\JWT\{JWT, ExpiredException, SignatureInvalidException};
use lgdz\Factory;
use Redis;
use lgdz\exception\JwtAuthException;

class JwtAuth
{
    // 密钥
    private $secret = '';
    // 用户登录凭证缓存KEY
    private $ticket_key = 'user_ticket';
    /**
     * redis操作实例
     * @var Redis
     */
    private $redis = null;

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getTicketKey(): string
    {
        return $this->ticket_key;
    }

    /**
     * @param string $ticket_key
     */
    public function setTicketKey(string $ticket_key): void
    {
        $this->ticket_key = $ticket_key;
    }

    /**
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @param Redis $redis
     */
    public function setRedis($redis): void
    {
        $this->redis = $redis;
    }

    /**
     * @throws JwtAuthException
     */
    private function checkRedis()
    {
        if (is_null($this->redis)) {
            throw new JwtAuthException('Redis未初始化');
        }
    }

    /**
     * 签发（默认缓存1天）
     * @param $uid 用户ID
     * @param array $body 附加额外数据
     * @param int $expire 过期时间（秒）
     * @return array
     * @throws JwtAuthException
     */
    public function issue($uid, array $body = [], int $expire = 86400): array
    {
        $nowTime = time();
        // 过期时间
        $timestamp = $nowTime + $expire;
        $payload = [
            // 签发时间
            'iat'  => $nowTime,
            'exp'  => $timestamp,
            'body' => array_merge(['uid' => $uid, 'ticket' => Factory::container()->helper->randomString(50)], $body)
        ];
        $this->checkRedis();
        $this->redis->hSet($this->ticket_key, (string)$uid, serialize($payload));
        return [JWT::encode($payload, $this->secret), date('Y-m-d H:i:s', $timestamp)];
    }

    /**
     * 验证
     * @param string $Authorization
     * @param bool $checkTicket
     * @return mixed
     * @throws JwtAuthException
     */
    public function check(string $Authorization, bool $checkTicket = false)
    {
        try {
            $decoded = JWT::decode($Authorization, $this->secret, array('HS256'));
        } catch (SignatureInvalidException $e) {
            throw new JwtAuthException('签名失败');
        } catch (ExpiredException $e) {
            throw new JwtAuthException('登录凭证过期');
        } catch (\Throwable $e) {
            throw new JwtAuthException($e->getMessage());
        }
        // ticket验证
        if ($checkTicket) {
            $uid = $decoded->body->uid;
            $this->checkRedis();
            $payload = $this->redis->hGet($this->ticket_key, (string)$uid);
            if (!$payload) {
                throw new JwtAuthException('ticket失效，请重新登录');
            }
            $payload = unserialize($payload);
            if ($payload['body']['ticket'] !== $decoded->body->ticket) {
                throw new JwtAuthException('ticket失效，请重新登录');
            } elseif ($decoded->exp < time()) {
                throw new JwtAuthException('ticket过期，请重新登录');
            }
        }
        $decoded->body->exp = $decoded->exp;
        return $decoded->body;
    }
}