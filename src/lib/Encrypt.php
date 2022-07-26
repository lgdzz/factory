<?php

declare (strict_types=1);

namespace lgdz\lib;

use Exception;
use lgdz\Factory;

/**
 * 接口加密解密
 * Class Encrypt
 * @package lgdz\lib
 */
class Encrypt
{
    public function encode(string $string, int $offset = 0, int $length = 9): string
    {
        $salt = Factory::container()->helper->randomString($length);
        $base64 = base64_encode($string);
        if ($offset === 0) {
            return $salt . $base64;
        } else {
            $left = substr($base64, 0, $offset);
            $right = substr($base64, $offset);
            return $left . $salt . $right;
        }
    }

    public function decode(string $string, $offset = 0, int $length = 9): string
    {
        if ($offset === 0) {
            return base64_decode(substr($string, $length));
        } else {
            $left = substr($string, 0, $offset);
            $right = substr($string, $offset + $length);
            return base64_decode($left . $right);
        }
    }
}