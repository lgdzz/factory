<?php

declare (strict_types=1);

namespace lgdz\lib;

use liliuwei\pscws4\PSCWS4API;

/**
 * 拆分关键词
 * Class SplitKeyword
 * @package lgdz\lib
 */
class SplitKeyword
{
    protected $api;

    public function __construct()
    {
        $this->api = new PSCWS4API;
    }

    public function get(string $string): array
    {
        return $this->api->PSCWS4($string);
    }

    public function api(): PSCWS4API
    {
        return $this->api;
    }
}