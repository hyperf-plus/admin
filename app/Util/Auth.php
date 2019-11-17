<?php


namespace App\Util;


use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class Auth
{

    /**
     * 忽略节点
     * @return array
     */
    public static function ignores()
    {
        return ['/admin/login/index', '/admin/login/refreshToken'];
    }

    /**
     * 检查节点权限
     * @param int $role_id
     * @param string $node
     * @return bool
     */
    public static function checkNode(int $role_id, string $node)
    {
        //todo 如果当前登录会员是admin账号，则开放所有权限
        //todo 基于redis的bitmap实现的额权限校验  sad..没有实现这个功能，用集合方式实现

        $key = Prefix::authNodes($role_id);

        $redis = Redis::getInstance();

        return $redis->sIsMember($key, md5($node));

    }


    /**
     * 是否已经登录
     */
    public static function isLogin()
    {

    }

}