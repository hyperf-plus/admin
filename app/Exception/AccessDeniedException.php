<?php


namespace App\Exception;


class AccessDeniedException extends \Exception
{
    public function __construct()
    {
        parent::__construct("没有相应权限", 401, null);
    }
}