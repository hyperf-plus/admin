<?php
declare(strict_types=1);

namespace Mzh\Admin\Service;


use Hyperf\HttpServer\Router\DispatcherFactory;
use Mzh\Admin\Model\AuthGroup;
use Mzh\Admin\Model\AuthRule;
use Mzh\Admin\Model\UserRole;

class AuthService
{


    public function getMenuRoleIds($menu_id)
    {
        if (!$menu_id) {
            return [];
        }
        return AuthGroup::query()
            ->select(['id'])
            ->where('router_id', $menu_id)
            ->get()
            ->pluck('id')
            ->toArray();
    }


    public function getRoleUserIds($role_id)
    {
        if (!$role_id) {
            return [];
        }

        return UserRole::query()
            ->select(['user_id'])
            ->where('role_id', $role_id)
            ->get()
            ->pluck('user_id')
            ->toArray();
    }

    public function getRoleTree()
    {
        $roles = AuthRule::query()->select(['id as value', 'name as label', 'pid'])
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->limit(200)->get()->toArray();
        return generate_tree($roles, 'pid', 'value');

    }

    public function getSystemRouteOptions()
    {
        $router = getContainer(DispatcherFactory::class)->getRouter('http');
        $data = $router->getData();
        $options = [];
        foreach ($data as $routes_data) {
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
                    $route_key = "$http_method::{$route}";
                    $options[] = [
                        'value' => $route_key,
                        'label' => $route_key,
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * 构造角色权限设置options
     *
     * @param int $role_id
     * @return array
     */
    public function getPermissionOptions($role_id = 0, $module = 'system')
    {
        // todo 配置化
        $options = [
            [
                'value' => 'default',
                'label' => '默认',
                'children' => make(MenuService::class)->tree(['module' => 'default']),
            ],
            [
                'value' => 'system',
                'label' => '系统',
                'children' => make(MenuService::class)->tree(['module' => 'system']),
            ],
        ];
        $info = AuthRule::query()->find($role_id);
        if (empty($info)){
            return [[], $options];
        }
        //  $values = array_merge($values, $this->getRolePermissionValues($router_ids, 'system'));
        $values = $this->getRolePermissionValues($info['menu_auth'], $info['module']);
        return [$values, $options];
    }

    private function getRolePermissionValues($router_ids = 0, $module = 'system')
    {
        if (empty($router_ids)) {
            return [];
        }
        $data = [];
        $routers = make(MenuService::class)->tree([
            'id' => $router_ids,
        ]);
        if (!empty($routers)) {
            $paths = array_keys(tree_2_paths($routers, $module));
            foreach ($paths as $path) {
                $data[] = explode('-', $path);
            }
        }
        return $data;
    }
}