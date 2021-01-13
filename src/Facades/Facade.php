<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Facades;

class Facade
{
    protected static $instance = [];

    public function __construct()
    {
    }

    /**
     * @param $method
     * @param $arg
     */
    public static function __callStatic($method, $arg)
    {
        $instance = static::getInstance(static::getFacadeAccessor());
        return call_user_func_array([$instance, $method], $arg);
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
    }
}
