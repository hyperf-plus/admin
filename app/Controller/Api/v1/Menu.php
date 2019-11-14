<?php

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use App\Exception\StatusException;
use App\Model\Admin;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class Ads
 * @package app\api\controller\v1
 */
class Menu extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一个菜单
            'add.menu.item' => ['addMenuItem'],
            // 获取一个菜单
            'get.menu.item' => ['getMenuItem'],
            // 编辑一个菜单
            'set.menu.item' => ['setMenuItem'],
            // 删除一个菜单
            'del.menu.item' => ['delMenuItem'],
            // 获取菜单列表
            'get.menu.list' => ['getMenuList'],
            // 根据Id获取导航数据
            'get.menu.id.navi' => ['getMenuIdNavi'],
            // 根据Url获取导航数据
            'get.menu.url.navi' => ['getMenuUrlNavi'],
            // 批量设置是否导航
            'set.menu.navi' => ['setMenuNavi'],
            // 设置菜单排序
            'set.menu.sort' => ['setMenuSort'],
            // 根据编号自动排序
            'set.menu.index' => ['setMenuIndex'],
            // 设置菜单状态
            'set.menu.status' => ['setMenuStatus'],
            // 根据权限获取菜单列表
            'get.menu.auth.list' => ['getMenuAuthList'],
        ];
    }

}
