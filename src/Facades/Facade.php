<?php
declare(strict_types=1);

namespace HPlus\Admin\Facades;

class Facade
{
    protected static $instance = [];

    public function __construct()
    {
        //
    }

    protected static function getInstance($classname)
    {
        if (isset(self::$instance[$classname]) && self::$instance[$classname] instanceof $classname) {
            return self::$instance[$classname];
        }
        self::$instance[$classname] = make($classname);
        return self::$instance[$classname];
    }

    protected static function getFacadeAccessor()
    {
        //
    }

    /**
     * @param $method
     * @param $arg
     */
    public static function __callstatic($method, $arg)
    {
        $instance = static::getInstance(static::getFacadeAccessor());
        return call_user_func_array(array($instance, $method), $arg);
    }
}