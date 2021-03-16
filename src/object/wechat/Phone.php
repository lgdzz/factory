<?php

namespace lgdz\object\wechat;

class Phone
{
    public $phone;

    public function __construct(array $data)
    {
        $this->phone = $data['purePhoneNumber'] ?? '';
    }
}