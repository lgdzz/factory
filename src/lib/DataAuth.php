<?php

declare (strict_types=1);

namespace lgdz\lib;

/**
 * Class DataAuth 数据对称验证
 * @package lgdz\lib
 */
class DataAuth extends InstanceClass implements InstanceInterface
{
    // 密钥
    private $secret;

    /**
     * Auth constructor.
     * @param $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    // 生成签名
    public function sign(array $data): string
    {
        // 检查字段类型如果为数组时转为字符串类型
        array_walk($data, function (&$value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        });

        $data['secret'] = $this->secret;
        ksort($data);
        $string = http_build_query($data);
        return md5($string);
    }

    // 验证签名
    public function verify(array $data): bool
    {
        $sign = $data['sign'] ?? null;
        if (is_null($sign)) {
            return false;
        }
        unset($data['sign']);
        return $this->sign($data) === $sign;
    }
}