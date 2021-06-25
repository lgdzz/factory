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
        foreach ($input as $name => $value) {
            $this->body[$name] = $value;
        }
    }

    public function __set($name, $value)
    {
        $this->body[$name] = $value;
    }

    public function __get($name)
    {
        return $this->body[$name] ?? null;
    }
}