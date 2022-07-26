<?php

namespace lgdz\exception;

use Throwable;

class CaptchaException extends \RuntimeException
{
    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}