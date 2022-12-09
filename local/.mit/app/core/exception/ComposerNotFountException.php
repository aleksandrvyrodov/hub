<?php

namespace MIT\App\Core\Exception;

use \Throwable;

class ComposerNotFountException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}