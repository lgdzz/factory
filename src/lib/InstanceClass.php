<?php

namespace lgdz\lib;

use lgdz\Factory;

class InstanceClass
{
    /**
     * @var Factory
     */
    protected $factory;

    public function getFactory(): Factory
    {
        return $this->factory;
    }

    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function isUnique(): bool
    {
        return true;
    }
}