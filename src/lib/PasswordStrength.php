<?php

declare (strict_types=1);

namespace lgdz\lib;

use Exception;

// 密码强度
class PasswordStrength
{
    private static $forbidden_keywords = ['root', 'admin', 'oracle', 'system', 'mysql'];
    private static $allow_special_characters = '~!@#$%^&*()[]{}|:\'+="<>?,./;\\\_-’‘？、。，；：“”」「【】·`《》￥…';

    /**
     * @throws Exception
     */
    private static function charArray(string $password): array
    {
        $char_i = $char_a = $char_A = $char_t = [];
        $chars = preg_split('/(?<!^)(?!$)/u', $password);
        foreach ($chars as $char) {
            if (is_numeric($char)) {
                $char_i[] = $char;
                continue;
            }
            $str = ord($char);
            if ($str > 64 && $str < 91) {
                //大写字母
                $char_A[] = $char;
                continue;
            }
            if ($str > 96 && $str < 123) {
                //小写字母
                $char_a[] = $char;
                continue;
            }
            //这里的特殊字符指
            if (strpos(static::$allow_special_characters, $char) !== false) {
                $char_t[] = $char;
                continue;
            }
            //其他一切字符
            throw new Exception("密码含有系统不允许的特殊字符");
        }
        return [$char_i, $char_a, $char_A, $char_t];
    }

    /**
     * @throws Exception
     */
    private static function checkLength(string $password, int $min_password_limit, int $max_password_limit)
    {
        if (mb_strlen($password) > $max_password_limit || mb_strlen($password) < $min_password_limit) {
            throw new Exception('密码长度必须是' . $min_password_limit . '到' . $max_password_limit . '位');
        }
    }

    /**
     * @param $password
     * @param string $username
     * @param int $min_password_limit
     * @param int $max_password_limit
     * @param array $strength 1-数字|2-小写字母|3-大写字母|4-特殊字符
     * @return void
     * @throws Exception
     */
    public static function validate($password, string $username = '', int $min_password_limit = 8, int $max_password_limit = 20, array $strength = [1, 2, 3, 4]): void
    {

        static::checkLength($password, $min_password_limit, $max_password_limit);

        [$char_i, $char_a, $char_A, $char_t] = static::charArray($password);

        if (in_array(1, $strength) && empty($char_i)) {
            throw new Exception('密码必须包含数字');
        } elseif (in_array(2, $strength) && empty($char_a)) {
            throw new Exception('密码必须包含小写字母');
        } elseif (in_array(3, $strength) && empty($char_A)) {
            throw new Exception('密码必须包含大写字母');
        } elseif (in_array(4, $strength) && empty($char_t)) {
            throw new Exception('密码必须包含特殊字符');
        }
        //关键字
        $forbidden_key = static::$forbidden_keywords;
        if (!empty($username)) {
            array_push($forbidden_key, strtolower($username));
        }
        foreach ($forbidden_key as $keyword) {
            if (strpos(strtolower($password), $keyword) !== false) {
                throw new Exception('密码包含系统禁止的词汇或包含用户名');
            }
        }
    }

    /**
     * 密码级别，0-4
     * @param $password
     * @param int $min_password_limit
     * @param int $max_password_limit
     * @return int
     * @throws Exception
     */
    public static function level($password, int $min_password_limit = 8, int $max_password_limit = 20): int
    {

        static::checkLength($password, $min_password_limit, $max_password_limit);

        $level = 0;

        [$char_i, $char_a, $char_A, $char_t] = static::charArray($password);

        !empty($char_i) && $level++;
        !empty($char_a) && $level++;
        !empty($char_A) && $level++;
        !empty($char_t) && $level++;

        return $level;
    }
}