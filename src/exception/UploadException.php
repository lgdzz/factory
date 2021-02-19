<?php

namespace lgdz\exception;

use Throwable;

class UploadException extends \RuntimeException
{
    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}