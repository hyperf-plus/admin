<?php

namespace HPlus\Admin\Library;

use HPlus\Admin\Contracts\PermissionInterface;
use HPlus\Admin\Model\Admin\UserPermission;
use HPlus\Route\Annotation\AdminController;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\HttpServer\Router\DispatcherFactory;
use HPlus\Admin\Contracts\AuthInterface;
use HPlus\Admin\Service\AuthService;
use HPlus\Route\Annotation\ApiController;
use Hyperf\Utils\Str;
use Mzh\Helper\RunTimes;
use Psr\Container\ContainerInterface;
use Qbhy\HyperfAuth\AuthManager;
use Psr\SimpleCache\CacheInterface;

class Permission implements PermissionInterface
{
    private static $cacheName = 'permission:';
    private static $cacheRoleName = 'role:';
    private $authNode = [];
    private $roles = [];
    private $ignore = [
    ];
    private $userOpen = [];
    private $auth;

    /**
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(AuthManager $auth, CacheInterface $cache)
    {
        $this->auth = $auth;
        $this->cache = $cache;
        $this->scanPermission();
    }

    public function isUserOpen($currUrl): bool
    {
        return in_array($currUrl, $this->userOpen);
    }

    public function can($method, $route): bool
    {
        $url = $method . '::' . $route;
        #判断当前URL是否是开放资源
        if ($this->isOpen($url)) {
            return true;
        }
        #判断该资源是否对登录用户开放
        if ($this->auth->check() && $this->isUserOpen($url)) {
            return true;
        }
        #先检测自身权限
        $allPermission = $this->userPermission();
        if ($this->hasPermission($method, $route, $allPermission)) {
            return true;
        }
        #检测角色权限
        $roles = $this->getUserRoles();
        $allPermission = [];
        foreach ($roles as $slug) {
            if (!isset($this->authNode[$slug])) continue;
            if ($this->hasPermission($method, $route, $this->authNode[$slug])) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($slug, $userId = null): bool
    {
        return in_array($slug, $this->getUserRoles($userId));
    }

    public function hasPermission($method, $route, $allPermission = []): bool
    {
        $url = $method . '::' . $route;
        #检测全部权限
        if (in_array('*', $allPermission)) {
            return true;
        }
        $arr = explode('/', $route);
        if (count($arr) > 2) {
            $arr[count($arr) - 1] = '*';
        }
        $nodeAll = 'ANY::' . implode('/', $arr);

        #检测URL节点下全部权限
        if (in_array($nodeAll, $allPermission)) {
            return true;
        }
        #检测URL权限
        if (in_array($url, $allPermission)) {
            return true;
        }
        return false;
    }

    public function getUserRoles($userId = null): array
    {
        $allRoles = $this->cache->get($this->getRoleKey($userId));
        if ($allRoles !== null) {
            return (array)$allRoles;
        }
        #取用户角色id
        if ($userId == null) {
            $user = $this->auth->user();
        } else {
            $userModel = config('admin.database.users_model');
            $user = $userModel::query()->find($userId);
        }
        $allRoles = $user->roles()->pluck('slug')->toArray();
        $this->cache->set($this->getRoleKey($userId), $allRoles);
        return (array)$allRoles;
    }

    #分开处理主要是为了动态管理权限、权限组实时生效
    protected function userPermission()
    {
        $allPermission = $this->cache->get($this->getKey());
        if ($allPermission !== null) {
            return (array)$allPermission;
        }
        $allPermission = [];
        #获取自身权限
        $userPerminssion = $this->auth->user()->permissions()->get();
        foreach ($userPerminssion as $item) {
            $allPermission = array_merge($allPermission, $item->getAttribute('path'));
        }
        $this->cache->set($this->getKey(), $allPermission);
        return (array)$allPermission;
    }

    protected function getKey($id = null)
    {
        if ($id === null) $id = $this->auth->user()->getId();
        return self::$cacheName . $id;
    }

    protected function getRoleKey($id = null)
    {
        if ($id === null) $id = $this->auth->user()->getId();
        return self::$cacheRoleName . $id;
    }

    /**
     * @param $url
     */
    public function removeIgnore($url)
    {
        if (in_array($url, $this->ignore)) {
            unset($this->ignore[array_search($url, $this->ignore)]);
        }
    }

    public function setUserOpen($url)
    {
        if (!in_array($url, $this->userOpen)) {
            $this->userOpen[] = $url;
        }
    }

    public function setIgnore($url)
    {
        if (!in_array($url, $this->ignore)) {
            $this->ignore[] = $url;
        }
    }

    public function isOpen($currUrl)
    {
        return in_array($currUrl, $this->ignore);
    }

    public function getIgnore()
    {
        return $this->ignore;
    }

    public function reloadUser($id)
    {
        $this->cache->delete($this->getKey($id));
        $this->cache->delete($this->getRoleKey($id));
    }

    public function loadRoles($reload = false)
    {
        if (!empty($this->authNode) && $reload == false) {
            return $this->authNode;
        }
        #获取角色权限
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $rolesPerminssion = (new $roleModel)->get();
        foreach ($rolesPerminssion as $item) {
            $allPermission = [];
            foreach ($item->permissions()->get() as $permission) {
                $allPermission = array_merge($allPermission, $permission->getAttribute('path'));
            }
            $this->authNode[$item->slug] = array_unique($allPermission);
        }
        return true;
    }

    /**
     * 首次会执行并载入内存
     * @return mixed|void
     */
    public function scanPermission()
    {
        $DispatcherFactory = getContainer(DispatcherFactory::class);
        $list = $DispatcherFactory->getRouter('http');
        $this->ignore = [];
        $this->loadRoles(true);
        #更新权限菜单
        foreach ($list->getData() as $routes_data) {
            foreach ($routes_data as $http_method => $routes) {
                $route_list = [];
                if (isset($routes[0]['routeMap'])) {
                    foreach ($routes as $map) {
                        array_push($route_list, ...$map['routeMap']);
                    }
                } else {
                    $route_list = $routes;
                }
                foreach ($route_list as $route => $v) {
                    // 过滤掉脚手架页面配置方法
                    $callback = is_array($v) ? ($v[0]->callback) : $v->callback;
                    if (!is_array($callback)) {
                        continue;
                    }
                    $route = is_string($route) ? rtrim($route) : rtrim($v[0]->route);
                    list($className, $methodName) = $callback;
                    $metadata = AnnotationCollector::get($className);
                    $userOpenAll = false;
                    $OpenAll = false;
                    if (isset($metadata['_c'][ApiController::class])) {
                        $userOpenAll = $metadata['_c'][ApiController::class]->userOpen;
                        $OpenAll = !$metadata['_c'][ApiController::class]->security;
                    }
                    if (isset($metadata['_c'][AdminController::class])) {
                        $userOpenAll = $metadata['_c'][AdminController::class]->userOpen;
                        $OpenAll = !$metadata['_c'][AdminController::class]->security;
                    }
                    foreach ($metadata['_m'][$methodName] ?? [] as $item) {
                        if (!$item instanceof Mapping) continue;
                        if (property_exists($item, 'security') || $OpenAll) {
                            $security = $item->security;
                            if (!$security || $OpenAll) {
                                $url = "$http_method::{$route}";
                                $this->setIgnore($url);
                            }
                        }
                        if (property_exists($item, 'userOpen') || $userOpenAll) {
                            if ($item->userOpen || $userOpenAll) {
                                $url = "$http_method::{$route}";
                                $this->setUserOpen($url);
                            }
                        }
                    }
                }
            }
        }

    }
}