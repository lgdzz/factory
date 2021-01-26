<?php

declare (strict_types=1);

namespace lgdz\lib;

use lgdz\exception\CaptchaException;

class Captcha extends InstanceClass implements InstanceInterface
{
    private $secret = '';
    private $point = true;
    private $line = true;
    private $content = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function setLevelEasy()
    {
        $this->content = '0123456789';
        $this->point   = false;
        $this->line    = false;
        return $this;
    }

    public function build(): array
    {
        ob_start();
        $image   = imagecreatetruecolor(100, 30);
        $bgcolor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgcolor);
        $content = $this->content;
        $captcha = '';
        for ($i = 0; $i < 4; $i++) {
            // 字体大小
            $fontsize = 5;
            // 字体颜色
            $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            // 设置字体内容
            $fontcontent = substr($content, mt_rand(0, strlen($content)), 1);
            $captcha     .= $fontcontent;
            // 显示的坐标
            $x = ($i * 100 / 4) + mt_rand(5, 10);
            $y = mt_rand(5, 10);
            // 填充内容到画布中
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
        }
        if ($this->point) {
            for ($$i = 0; $i < 200; $i++) {
                $pointcolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
                imagesetpixel($image, mt_rand(1, 99), mt_rand(1, 29), $pointcolor);
            }
        }
        if ($this->line) {
            for ($i = 0; $i < 3; $i++) {
                $linecolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
                imageline($image, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $linecolor);
            }
        }
        imagepng($image);
        imagedestroy($image);
        $data      = ob_get_clean();
        $uuid      = $this->factory->Helper()->randomString(32);
        $timestamp = time();
        return [
            'captcha' => 'data:image/png;base64,' . base64_encode($data),
            'input'   => [
                'timestamp' => $timestamp,
                'uuid'      => $uuid,
                'check'     => $this->encode($captcha, $uuid, $timestamp),
                'code'      => ''
            ]
        ];
    }

    public function check(array $input): void
    {
        $timestamp = $input['timestamp'] ?? null;
        $uuid      = $input['uuid'] ?? null;
        $check     = $input['check'] ?? null;
        $code      = $input['code'] ?? null;
        if (is_null($timestamp) || is_null($uuid) || is_null($check) || is_null($code)) {
            throw new CaptchaException('验证码参数不完整');
        } elseif (($timestamp + 300) <= time()) {
            throw new CaptchaException('验证码已失效');
        } elseif ($check !== $this->encode($code, $uuid, $timestamp)) {
            throw new CaptchaException('验证码不正确');
        }
    }

    private function encode($captcha, $uuid, $timestamp)
    {
        return md5(md5(strtolower($captcha) . $uuid . $timestamp) . $this->secret);
    }
}