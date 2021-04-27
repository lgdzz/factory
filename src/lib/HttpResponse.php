<?php

declare (strict_types=1);

namespace lgdz\lib;

class HttpResponse
{
    public $string_json = false;

    protected function _out(int $error, string $message = 'Success', int $status = 0, $data = null)
    {
        $data = [
            'error'     => $error,
            'message'   => $message,
            'status'    => $status,
            'data'      => $data,
            'timestamp' => time()
        ];
        return $this->string_json ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
    }

    public function ok($data = null)
    {
        return $this->_out(0, 'Success', 0, $data);
    }

    public function success(string $message, $data = null)
    {
        return $this->_out(0, $message, 0, $data);
    }

    public function bad(string $message = 'Error', int $status = 0)
    {
        return $this->_out(1, $message, $status);
    }

    public function fail(string $message, int $status = 0, $data = null)
    {
        return $this->_out(1, $message, $status, $data);
    }
}