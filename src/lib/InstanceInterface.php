<?php

namespace lgdz\lib;

use lgdz\Factory;

Interface InstanceInterface
{
    // true-单例类
    public function isUnique(): bool;

    public function getFactory(): Factory;

    public function setFactory(Factory $factory);
}