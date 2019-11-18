<?php


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class FileException extends ServerException
{

    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}