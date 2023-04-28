<?php

namespace MIT\Model\FavoriteProduct\Exception;

use \Throwable;

class FPException extends \Exception implements Throwable
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}