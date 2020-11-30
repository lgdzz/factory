<?php

namespace lgdz\lib\workerman;

trait BuildTarget
{
    private function value(string $target)
    {
        return "{$this->appid}_{$target}";
    }

    public function target($to)
    {
        if (is_array($to)) {
            $to = array_map(function ($item) {
                return $this->value((string)$item);
            }, $to);
        } else {
            $to = $this->value((string)$to);
        }
        return $to;
    }
}