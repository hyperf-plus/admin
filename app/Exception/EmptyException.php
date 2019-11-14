<?php


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class EmptyException extends ServerException
{
    public function __construct($message = "数据不存在！", $code = 200, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}