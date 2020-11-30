<?php

declare (strict_types=1);

namespace lgdz\lib;

class HttpResponse extends InstanceClass implements InstanceInterface
{
    protected function _out(int $error, string $message = 'Success', int $status = 0, $data = null): string
    {
        return json_encode([
            'error'     => $error,
            'message'   => $message,
            'status'    => $status,
            'dataset'   => $data,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);
    }

    public function ok($data = null): string
    {
        return $this->_out(0, 'Success', 0, $data);
    }

    public function bad(string $message = 'Error', int $status = 0): string
    {
        return $this->_out(1, $message, $status);
    }

    public function success(string $message, $data = null)
    {
        return $this->_out(0, $message, 0, $data);
    }

    public function fail(string $message, int $status = 0, $data = null)
    {
        return $this->_out(1, $message, $status, $data);
    }
}