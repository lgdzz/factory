<?php

declare (strict_types=1);

namespace lgdz\lib;

use Curl\Curl;
use lgdz\Factory;
use lgdz\object\IdCard;

class Helper
{
    protected static $snakeCache = [];
    protected static $camelCache = [];
    protected static $studlyCache = [];

    // 随机字符串
    public function randomString(int $length = 5): string
    {
        $array = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    // 随机数字
    public function randomNumber(int $length = 5): string
    {
        $array = range(0, 9);
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    // 唯一单号（长度最小15，推荐最大19）
    public function orderNo(int $len = 15, \Closure $check = null): string
    {
        $order_no = date('ymdHis') . $this->randomNumber($len - 12);
        !is_null($check) && $check($order_no);
        return $order_no;
    }

    // 获取IP信息
    public function getIpInfo(string $ip = 'myip', string $access_key = 'alibaba-inc'): array
    {
        $request_data = ['ip' => $ip, 'accessKey' => $access_key];
        $result = Factory::container()->request->post('http://ip.taobao.com/outGetIpInfo', $request_data, function (Curl $curl) {
            $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        });
        if ($result && isset($result->code) && $result->code === 0) {
            $data = $result->data;
            return [
                'ip'     => $data->queryIp,
                'isp'    => sprintf('%s|%s|%s|%s', $data->country ?? '-', $data->region ?? '-', $data->city ?? '-', $data->isp ?? '-'),
                'result' => $data
            ];
        } else {
            return ['ip' => '未知', 'isp' => '未知', 'result' => null];
        }
    }

    // 二维数组排序
    public function arrayMultiSort(array $list, string $column_name, int $sort = SORT_ASC): array
    {
        $column = array_column($list, $column_name);
        array_multisort($column, $sort, $list);
        return $list;
    }

    /**
     * 检查字符串中是否包含某些字符串(or)
     * @param string $haystack 如：Hello World
     * @param string|array $needles 如：Hello 或 ['Hello', 'World']
     * @return bool
     */
    public function contains(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查字符串中是否包含某些字符串(and)
     * @param string $haystack 如：Hello World
     * @param string|array $needles 如：Hello 或 ['Hello', 'World']
     * @return bool
     */
    public function containsAnd(string $haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public function endsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === $this->substr($haystack, -$this->length($needle))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public function startsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 字符串转小写
     * @param string $value
     * @return string
     */
    public function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param string $value
     * @return string
     */
    public function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * 获取字符串的长度
     * @param string $value
     * @return int
     */
    public function length(string $value): int
    {
        return mb_strlen($value);
    }

    /**
     * 截取字符串
     * @param string $string
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public function substr(string $string, int $start, int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * 字符串按每个字转数组
     * @param string $string
     * @return array
     */
    public function stringToArray(string $string): array
    {
        $length = mb_strlen($string, 'UTF-8');
        $array = [];
        for ($i = 0; $i < $length; $i++) {
            $array[] = mb_substr($string, $i, 1, 'UTF-8');
        }
        return $array;
    }

    /**
     * 字符串提取词组
     * @param string $string
     * @return array
     */
    public function stringToKeywords(string $string): array
    {
        return Factory::container()->split_keyword->get($string);
    }

    /**
     * 字符串比对重叠词组数
     * @param string $str1
     * @param string $str2
     * @return int
     */
    public function stringKeywordMatchRate(string $str1, string $str2): int
    {
        return count(array_intersect($this->stringToKeywords($str1), $this->stringToKeywords($str2)));
    }

    /**
     * 字符串比对从一组字符串中找出重叠词组数最多的一个字符串
     * @param string $str1
     * @param array $strings
     * @return mixed|string
     */
    public function stringKeywordMatchHighWeight(string $str1, array $strings): string
    {
        $str2 = '';
        $len = -1;
        foreach ($strings as $str) {
            $str = (string)$str;
            $weight = $this->stringKeywordMatchRate($str1, $str);
            if ($weight > $len) {
                $len = $weight;
                $str2 = $str;
            }
        }
        return $str2;
    }

    /**
     * 驼峰转下划线
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = $this->lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * 下划线转驼峰(首字母小写)
     * @param string $value
     * @return string
     */
    public function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst($this->studly($value));
    }

    /**
     * 下划线转驼峰(首字母大写)
     * @param string $value
     * @return string
     */
    public function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * 身份证号解析信息
     * @param string $id_card
     * @return array
     */
    public function idCardAnalysis(string $id_card)
    {
        $region_code = $this->substr($id_card, 0, 6);
        $year = $this->substr($id_card, 6, 4);
        $birthday = sprintf('%s-%s-%s', $year, $this->substr($id_card, 10, 2), $this->substr($id_card, 12, 2));
        $age = date('Y') - $year;
        $gender = $this->substr($id_card, 16, 1) % 2 === 0 ? '女' : '男';
        return ['region_code' => $region_code, 'birthday' => $birthday, 'age' => $age, 'gender' => $gender];
    }

    /**
     * 身份证号解析信息
     * @param string $id_card
     * @return IdCard
     */
    public function idCardAnalysisObject(string $id_card): IdCard
    {
        return new IdCard($this->idCardAnalysis($id_card));
    }

    /**
     * 随机文件名称（唯一）
     * @return string
     */
    public function randomName()
    {
        return md5(uniqid($this->orderNo(19)));
    }

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @param int $precision 小数位数
     * @return string
     */
    function formatBytes($size, $delimiter = '', $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . $delimiter . $units[$i];
    }
}