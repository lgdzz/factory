<?php

declare (strict_types=1);

namespace lgdz\lib;

use Exception;

class Password
{
    public function build(string $password, string $salt): string
    {
        return $this->encode($password, $salt);
    }

    public function check(string $input_password, string $salt, string $right_password): bool
    {
        return $right_password === $this->encode($input_password, $salt);
    }

    private function encode(string $password, string $salt): string
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 密码强度验证
     * @param $password
     * @param string $username
     * @param int $min_password_limit
     * @param int $max_password_limit
     * @param array $strength 1-数字|2-小写字母|3-大写字母|4-特殊字符
     * @return void
     * @throws Exception
     */
    public function checkStrength($password, string $username = '', int $min_password_limit = 8, int $max_password_limit = 20, array $strength = [1, 2, 3, 4]): void
    {
        PasswordStrength::validate($password, $username, $min_password_limit, $max_password_limit, $strength);
    }

    /**
     * 密码级别（数字+小写字母+大写字母+特殊字符=4）
     * @throws Exception
     */
    public function getLevel(string $password, $min_password_limit = 8, int $max_password_limit = 20): int
    {
        return PasswordStrength::level($password, $min_password_limit, $max_password_limit);
    }

    /**
     * @throws Exception
     */
    public function checkLevel(string $password, $min_password_limit = 8, int $max_password_limit = 20, int $safeLevel = 4): void
    {
        if ($this->getLevel($password, $min_password_limit, $max_password_limit) < $safeLevel) {
            if ($safeLevel === 4) {
                $message = '密码必须同时包含数字、小写字母、大写字母和特殊字符';
            } else {
                $message = '密码必须包含数字、小写字母、大写字母、特殊字符' . $safeLevel . '种或以上的组合';
            }
            throw new Exception($message);
        }
    }
}