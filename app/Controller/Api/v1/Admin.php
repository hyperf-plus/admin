<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * 订单管理
 * Class OrderController
 * @AutoController()
 * @package App\Controller\Order
 */
class Admin extends AdminBase
{

    /**
     * 方法路由器
     * @var array
     */
    private static $route;
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 验证账号是否合法
            'check.admin.username' => ['checkAdminName', 'App\Service\Admin'],
            // 验证账号昵称是否合法
            'check.admin.nickname' => ['checkAdminNick', 'App\Service\Admin'],
            // 添加一个账号
            'add.admin.item' => ['addAdminItem'],
            // 编辑一个账号
            'set.admin.item' => ['setAdminItem'],
            // 批量设置账号状态
            'set.admin.status' => ['setAdminStatus'],
            // 修改一个账号密码
            'set.admin.password' => ['setAdminPassword'],
            // 重置一个账号密码
            'reset.admin.item' => ['resetAdminItem'],
            // 批量删除账号
            'del.admin.list' => ['delAdminList'],
            // 获取一个账号
            'get.admin.item' => ['getAdminItem'],
            // 获取账号列表
            'get.admin.list' => ['getAdminList'],
            // 注销账号
            'logout.admin.user' => ['logoutAdmin'],
            // 登录账号
            'login.admin.user' => ['loginAdmin'],
            // 刷新Token
            'refresh.admin.token' => ['refreshToken'],
        ];
    }
}