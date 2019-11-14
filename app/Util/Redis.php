<?php


namespace App\Util;


use Hyperf\Utils\ApplicationContext;
use Hyperf\Redis\RedisFactory;

class Redis
{

    private static $instance = [];

    public static function getInstance($db = 'default')
    {
        if (empty(self::$instance[$db])) {
            $instance = ApplicationContext::getContainer()->get(RedisFactory::class)->get($db);
            self::$instance[$db] = $instance;
        }

        return self::$instance[$db];
    }

}