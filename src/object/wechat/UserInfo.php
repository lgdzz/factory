<?php

namespace lgdz\object\wechat;

class UserInfo
{
    public $openid;
    public $nickname;
    public $gender;
    public $language;
    public $city;
    public $province;
    public $country;
    public $avatar;

    public function __construct(array $UserInfo)
    {
        $this->openid = $UserInfo['openId'] ?? '';
        $this->nickname = $UserInfo['nickName'] ?? '';
        $this->gender = $UserInfo['gender'] ?? 0;
        $this->language = $UserInfo['language'] ?? '';
        $this->city = $UserInfo['city'] ?? '';
        $this->province = $UserInfo['province'] ?? '';
        $this->country = $UserInfo['country'] ?? '';
        $this->avatar = $UserInfo['avatarUrl'] ?? '';
    }
}