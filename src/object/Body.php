<?php

namespace lgdz\object;

class Body
{
    /**
     * @var array
     */
    public $body;

    public function __construct(array $input)
    {
        foreach ($input as $key => $value) {
            $this->body[$key] = $value;
        }
    }

    public function __get($name)
    {
        return $this->body[$name] ?? null;
    }
}