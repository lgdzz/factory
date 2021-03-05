<?php

declare (strict_types=1);

namespace lgdz\lib;

use Overtrue\Pinyin\Pinyin as PinyinUtils;

class Pinyin extends InstanceClass implements InstanceInterface
{
    /**
     * @var PinyinUtils
     */
    private $pinyin;

    public function __construct()
    {
        $this->pinyin = new PinyinUtils;
    }

    public function pinyin()
    {
        return $this->pinyin;
    }

    /**
     * 生成字符串首字母
     * @param string $text
     * @return string
     */
    public function initial(string $text)
    {
        return $this->pinyin->abbr($text, PINYIN_KEEP_NUMBER);
    }
}