<?php

namespace lgdz\exception;

use Throwable;

class JwtAuthException extends \RuntimeException
{
    public function __construct($message = "", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}