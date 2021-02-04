<?php

declare (strict_types=1);

namespace lgdz\lib;

class Password extends InstanceClass implements InstanceInterface
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
}