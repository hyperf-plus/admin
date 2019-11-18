<?php

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use App\Exception\StatusException;
use App\Exception\SystemException;
use App\Model\Admin;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class Message
 * @package app\api\controller\v1
 */
class UserLevel extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 获取一个账号等级
            'get.user.level.item' => ['getLevelItem'],
            // 获取账号等级列表
            'get.user.level.list' => ['getLevelList'],
            // 添加一个账号等级
            'add.user.level.item' => ['addLevelItem'],
            // 编辑一个账号等级
            'set.user.level.item' => ['setLevelItem'],
            // 批量删除账号等级
            'del.user.level.list' => ['delLevelList'],
        ];
    }
}
