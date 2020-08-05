<?php

namespace Mzh\Admin\Library;


use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Mzh\Admin\Model\Admin\FrontRoutes;
use Mzh\Admin\Model\AuthRule;
use Mzh\Admin\Service\AuthService;
use Mzh\Admin\Service\ConfigService;

class Auth
{
    private $authMenu = [];
    private $ignore = [
        'POST::/api/user/login'
    ];

    public function __construct()
    {
        $this->restart();
    }

    public function hasPermission($userId, $url)
    {
        $roles = $this->getUserRole($userId);
        $menuAuth = $this->loadMenuAuth();
        foreach ($roles as $role) {
            if (in_array($url, ($menuAuth[$role] ?? []))) {
                return true;
            }
        }
        return false;
    }

    public function loadMenuAuth($reload = false)
    {
        if (!$reload && !empty($this->authMenu)) {
            return $this->authMenu;
        }
        $result = AuthRule::query()->where('status', 1)->get(['menu_auth', 'id'])->toArray();
        $rules = [];
        foreach ($result as $item) {
            //规则
            $role_id = $item['id'];
            $auth = (array)$item['menu_auth'];
            $ids = [];
            foreach ($auth as $tmp) {
                foreach ($tmp as $value) if (is_numeric($value)) $ids[] = $value;
            }
            $ids = array_values(array_unique($ids));
            $list = FrontRoutes::query()->whereIn('id', $ids)->where('permission', '!=', '')->get(['permission'])->toArray();
            foreach ($list as $value) {
                if (empty($value['permission'])) continue;
                if (!isset($rules[$role_id])) $rules[$role_id] = [];
                $rule = array_merge($rules[$role_id], $value['permission']);
                $rules[$role_id] = array_unique($rule);
            }
        }
        $this->authMenu = $rules;
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

    public function setIgnore($url)
    {
        if (in_array($url, $this->ignore)) {
            return;
        }
        $this->ignore[] = $url;
    }

    public function isSecurity($currUrl)
    {
        return !in_array($currUrl, $this->ignore);
    }

    public function getIgnore()
    {
        return $this->ignore;
    }

    public function restart()
    {
        $DispatcherFactory = getContainer(DispatcherFactory::class);
        $list = $DispatcherFactory->getRouter('http');
        $this->ignore = [];
        $this->authMenu = [];
        #更新权限菜单
        $this->loadMenuAuth(true);
        $security = true;
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
                    $security = true;
                    foreach ($metadata['_m'][$methodName] ?? [] as $item) {
                        if ($item instanceof Mapping) {
                            $security = $item->security;
                            break;
                        }
                    }
                    if (!$security) {
                        $url = "$http_method::{$route}";
                        $this->setIgnore($url);
                    }
                }
            }
        }

        #从数据库配置中取出白名单接口
        $permissions = ConfigService::getConfig('permissions');
        foreach ($permissions['open_api'] ?? [] as $url) {
            $this->setIgnore($url);
        }

        #从数据库配置中取出必须验证权限的接口
        foreach ($permissions['user_open_api'] ?? [] as $url) {
            $this->removeIgnore($url);
        }
    }

    private function getUserRole($userId)
    {
        $roles = json_decode(redis()->get('userRole:User' . $userId), true);
        if (empty($roles)) {
            $roles = make(AuthService::class)->getUserRoleIds($userId);
            redis()->set('userRole:User' . $userId, json_encode($roles));
        }
        return $roles;
    }

    private function getUserRoleAuth($userId)
    {


    }
}