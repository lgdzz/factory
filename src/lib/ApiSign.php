<?php

declare (strict_types=1);

namespace lgdz\lib;

use Exception;

/**
 * 接口签名
 * Class ApiSign
 * @package lgdz\lib
 */
class ApiSign
{
    // 密钥
    private $secret = '';

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * 生成签名
     * @param array $data
     * @return string
     */
    public function sign(array $data)
    {
        if (isset($data['sign'])) {
            unset($data['sign']);
        }
        $data['secret'] = $this->secret;
        ksort($data);
        return md5(http_build_query($data));
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function checkSign(array $data)
    {
        if (!isset($data['sign'])) {
            throw new Exception('sign未定义');
        } elseif ($this->sign($data) !== $data['sign']) {
            throw new Exception('签名不正确');
        } else {
            return true;
        }
    }
}