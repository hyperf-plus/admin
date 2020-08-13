<?php
declare(strict_types=1);

namespace Mzh\Admin\Service;


use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Mzh\Admin\Model\AuthGroup;
use Mzh\Admin\Model\AuthRule;
use Mzh\Admin\Model\UserRole;

class AuthService
{

    /**
     * @Inject()
     * @var MenuService
     */
    protected $menuService;

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
    public function getPermissionOptions($role_ids = [])
    {
//        if (!is_array($role_ids)) {
//            return [[], []];
//        }
//        $modules = [];
//        $data = [];
//        foreach ($role_ids as $item) {
//            foreach ($item as $value) if (!is_numeric($value)) $modules[] = $value; //  foreach ($item as $value) if (is_numeric($value)) $ids[] = $value; else $modules[] = $value;
//        }
        // $ids = array_values(array_unique($ids));
        //$modules = array_values(array_unique($modules));
        $data = [];
        $modules = ConfigService::getConfig('namespace');
        foreach ($modules as $module => $label) {
//            if (isSystemRole()) { #不是系统角色的话就只能显示自己权限范围内的数据
//            }
            $options[] = [
                'value' => $module,
                'label' => $label,
                'children' => $this->menuService->tree(['module' => $module]),
            ];
        }
        foreach ($options as $option) {
            if (!empty($option['children'])) {
                $paths = array_keys(tree_2_paths($option['children'], $option['value']));
                foreach ($paths as $path) {
                    $data[] = explode('-', $path);
                }
            }
        }
        return [$data, $options];
    }


    public function getUserRoleIds($user_id, $iss = 'admin')
    {
        if (!$user_id) {
            return [];
        }
        return UserRole::query()
            ->where('user_id', $user_id)
            ->where('module', $iss)
            ->get(['role_id'])
            ->pluck('role_id')
            ->toArray();
    }

}