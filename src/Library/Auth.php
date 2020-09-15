<?php

namespace Mzh\Admin\Library;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Mzh\Admin\Contracts\AuthInterface;
use Mzh\Admin\Service\AuthService;
use Mzh\Swagger\Annotation\ApiController;

class Auth implements AuthInterface
{
    private $authMenu = [];
    private $roles = [];
    private $ignore = [
        'POST::/api/user/login'
    ];
    private $userOpen = [];


    public function __construct()
    {
        $this->killCache();
    }

    public function isUserOpen($currUrl): bool
    {
        return in_array($currUrl, $this->userOpen);
    }

    public function hasPermission($userId, $iss, $url): bool
    {
        $roles = $this->getUserRole($userId, $iss);
        $menuAuth = $this->loadAuth();
        foreach ($roles as $role) {
            if (in_array($url, ($menuAuth[$role] ?? []))) {
                return true;
            }
        }
        return false;
    }

    public function loadAuth($reload = false)
    {
        if (!$reload && !empty($this->authMenu)) {
            return $this->authMenu;
        }
        $permissionModel = config('admin.database.permissions_model');
        $permission = new $permissionModel;
        $result = $permission->where('status', 1)->get(['path', 'id'])->toArray();
        $rules = [];
        foreach ($result as $item) {
            //规则
            $role_id = $item['id'];
            $auth = (array)$item['path'];
            $rules[$role_id] = array_unique($auth);
        }
        $this->roles = $rules;

        return $this->authMenu;
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
        return !in_array($currUrl, $this->ignore);
    }

    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * 首次会执行并载入内存
     * @return mixed|void
     */
    public function killCache()
    {
        $DispatcherFactory = getContainer(DispatcherFactory::class);
        $list = $DispatcherFactory->getRouter('http');
        $this->ignore = [];
        $this->authMenu = [];
        #更新权限菜单
        $this->loadAuth(true);
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

    private function getUserRole($userId)
    {
        $cacheKey = 'userRole:' .  $userId;
        $roles = json_decode(redis()->get($cacheKey), true);
        if (empty($roles)) {
            $roles = make(AuthService::class)->getUserRoleIds($userId);
            redis()->set($cacheKey, json_encode($roles));
        }
        return $roles;
    }

    private function getUserRoleAuth($userId)
    {


    }
}