<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    规则验证服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/30
 */
declare(strict_types=1);

namespace App\Service;

use App\Model\AuthRule;
use App\Model\Menu;
use Hyperf\Cache\Annotation\Cacheable;

class Auth
{
    /**
     * 菜单权限
     * @var array
     */
    private $menuAuth = [];

    /**
     * 白名单列表
     * @var array
     */
    private $whiteList = [];

    /**
     * 日志权限
     * @var array
     */
    private $logAuth = [];

    /**
     * 菜单数据
     * @var array
     */
    private $menuList = [];

    /**
     * 权限验证实例
     * @var object
     */
    protected static $auth = [];

    /**
     * 忽略节点
     * @return array
     */
    public function ignores()
    {
        return ['/admin/login/index', '/admin/login/refreshToken'];
    }

    /**
     * 验证权限
     * @access public
     * @param string $url Url(模块/控制器/操作名)
     * @return bool
     */
    public function check($url)
    {
        // 获取权限数据
        $rule = AuthRule::getMenuAuthRule($module, $groupId);
        if ($rule) {
            $this->menuAuth = $rule['menu_auth'];
            $this->logAuth = $rule['log_auth'];
            $this->whiteList = $rule['white_list'];
        }
        // 获取菜单数据
        $menu = Menu::getUrlMenuList($module);
        if ($menu && is_array($menu)) {
            $this->menuList[$groupId] = $menu;
        }


        /**
         * 不能直接返回,否则后台的权限勾选失去了意义
         * 而且部分API并不适合混用
         */
//        // 超级管理员直接返回
//        if (AUTH_SUPER_ADMINISTRATOR == get_client_group()) {
//            return true;
//        }

        // 转为小写
        $url = mb_strtolower($url, 'utf-8');

        // 核心数据是否存在
        if (empty($this->menuAuth) || empty($this->menuList)) {
            return false;
        }

        if (!isset($this->menuList[$url])) {
            return false;
        }

        $menuId = $this->menuList[$url]['menu_id'];
        if (in_array($menuId, $this->menuAuth)) {
            return true;
        }

        return false;
    }

    /**
     * @Cacheable(prefix="CommonAuth", ttl=9000, value="_#{module}_#{groupId}", listener="CommonAuth-update")
     * @param string $module
     * @param int $groupId
     * @return mixed
     */
    public function getAuth(string $module, int $groupId)
    {
        // 获取权限数据
        $rule = AuthRule::getMenuAuthRule($module, $groupId);
        $auth = [];
        $auth['module'] = $module;
        $auth['groupId'] = $groupId;
        if ($rule) {
            $auth['menu_auth'] = $rule['menu_auth'];
            $auth['log_auth'] = $rule['log_auth'];
            $auth['white_list'] = $rule['white_list'];
        }
        // 获取菜单数据

        $menu = Menu::getUrlMenuList($module);
        if ($menu && is_array($menu)) {
            $auth['menu_list'] = $menu;
        }
        return $auth;
    }


    /**
     * 验证Auth
     * @access private
     * @param string $model
     * @param int $group
     * @param string $node
     * @param string $method
     * @return string|true
     */
    public function checkAuth(string $model, int $group, string $node, string $method)
    {
        $key = $model.$group;
        // 初始化规则模块
        if (!isset(self::$auth[$key])) {
            self::$auth[$key] = make(\App\Model\Entity\Auth::class, [$this->getAuth($model, $group)]);
        }
        /**
         * @var \App\Model\Entity\Auth $auth
         */
        $auth = self::$auth[$key];
        // 批量API调用或调试模式不需要权限验证
        if ($method == 'Batch') {
            return true;
        }
        // 优先验证是否属于白名单接口(任何访问者都可访问)
        if ($auth->checkWhite($this->getAuthUrl($node, $method))) {
            return true;
        }
        // 再验证是否有权限
        if ($auth->check($this->getAuthUrl($node, $method))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否属于白名单
     * @access public
     * @param string $url Url(模块/控制器/操作名)
     * @return bool
     */
    public function checkWhite($url)
    {
        if (empty($this->whiteList)) {
            return false;
        }

        if (!isset($this->menuList[$url])) {
            return false;
        }

        $menuId = $this->menuList[$url]['menu_id'];
        if (in_array($menuId, $this->whiteList)) {
            return true;
        }

        return false;
    }

    /**
     * 记录日志
     * @access public
     * @param string $url Url(模块/控制器/操作名)
     * @param object $request 请求对象
     * @param array $result 处理结果
     * @param string $class 手动输入当前类
     * @param string $error 错误信息
     * @return void
     */
    public function saveLog($url, &$request, $result, $class, $error = '')
    {
        // 转为小写
        $url = mb_strtolower($url, 'utf-8');

        if (!isset($this->menuList[$url])) {
            return;
        }

        $menuId = $this->menuList[$url]['menu_id'];
        if (!in_array($menuId, $this->logAuth)) {
            return;
        }

        $data = [
            'client_type' => get_client_type(),
            'user_id' => get_client_id(),
            'username' => get_client_name(),
            'path' => $url,
            'module' => $class,
            'params' => $request->param(),
            'result' => false === $result ? $error : $result,
            'ip' => $request->ip(),
            'status' => false === $result ? 1 : 0,
        ];

        ActionLog::create($data);
    }

    /**
     * @param $controller
     * @param $method
     * @return string
     */
    private function getAuthUrl($controller, $method)
    {
        return trim(sprintf('%s/%s', $controller, $method),'/');
    }
}
