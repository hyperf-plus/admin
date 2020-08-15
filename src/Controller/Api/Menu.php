<?php
declare(strict_types=1);

namespace Mzh\Admin\Controller\Api;

use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Utils\Str;
use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Model\Admin\FrontRoutes;
use Mzh\Admin\Model\Admin\FrontRoutes as AdminMenu;
use Mzh\Admin\Model\Config;
use Mzh\Admin\Service\MenuService;
use Mzh\Admin\Traits\GetApiBatchDel;
use Mzh\Admin\Traits\GetApiCreate;
use Mzh\Admin\Traits\GetApiDelete;
use Mzh\Admin\Traits\GetApiDetail;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiRowChange;
use Mzh\Admin\Traits\GetApiSort;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Traits\GetApiUpdate;
use Mzh\Admin\Validate\FrontRoutesValidation;
use Mzh\Admin\Views\MenuView;
use Mzh\Helper\DbHelper\QueryHelper;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\Query;

/**
 * @ApiController(tag="后台-菜单模块")
 */
class Menu extends AbstractController
{
    use GetApiList;
    use GetApiUI;
    use GetApiUpdate;
    use GetApiDetail;
    use GetApiCreate;
    use GetApiSort;
    use GetApiDelete;
    use GetApiBatchDel;
    use GetApiRowChange;

    public $validateClass = FrontRoutesValidation::class;
    public $modelClass = AdminMenu::class;
    public $viewClass = MenuView::class;

    /**
     * 列表查询前操作，这里可用于筛选条件添加、也可在此做权数据权限二次验证等
     */
    public function _list_before(QueryHelper &$query)
    {
        $query->addData('pid', 0);
        $query->equal('module#tab_id,pid');
    }

    protected function _form_response_before($id, &$record)
    {
        if (isset($record['type']) && in_array($record['type'], [1, 2])
            && !empty($record['permission'])) {
            $record['permission'] = array_map(function ($item) use ($record) {
                if (!Str::contains($item, '::')) {
                    $http_method = FrontRoutes::$http_methods[$record['http_method']];
                    return "{$http_method}::{$item}";
                }
                return $item;
            }, $record['permission']);
        }
        $record['scaffold_action'] = $record['scaffold_action'] ? array_keys($record['scaffold_action']) : [];
        $record['pid'] = (new MenuService())->getPathNodeIds($id);
    }

    /**
     * 列表查询后操作，这里可用于列表数据二次编辑
     */
    public function _list_after(&$list)
    {
        foreach ($list as $item) {
            $item->hasChildren = $item->hasChildren();
        }
    }

    public function _form_before(&$data)
    {
        if ($this->isGet()) {
            return;
        }
        if ($data['type'] == 1) {
            if ($data['path'] == '#' || $data['path'] == '') {
                throw new BusinessException(1000, '菜单路由地址不能为空或"#"');
            }
            $paths = array_filter(explode('/', $data['path']));
            if (count($paths) > 5) {
                throw new BusinessException(1000, '路由地址层级过深>5，请设置精简一些');
            }
        } else {
            $data['path'] = '#';
        }
        $data['is_menu'] = intval($data['type'] == 2 ? 0 : $data['is_menu']);
        $data['is_scaffold'] = intval($data['is_scaffold'] ?? 0);

        $pid = array_pop($data['pid']);
        if (isset($data[$this->getPk()]) && $pid == $data[$this->getPk()]) {
            $pid = array_pop($data['pid']);
        }
        $data['pid'] = (int)$pid;
        if ($data['type'] > 1) {
            $parent_info = $this->getModel()->find($data['pid']);
            if (!$parent_info || $parent_info['type'] != 1) {
                throw new BusinessException(1000, '菜单类型为权限时请选择一个上级类目');
            }
        }
        $data['status'] = 1;
    }

    /**
     * @GetApi(summary="查询子节点",security=true)
     * @Query(key="id")
     * @throws \Exception
     */
    public function childs()
    {
        $pid = intval($this->request->query('id', 0));
        $list = AdminMenu::query()->where('pid', $pid)->where('status', 1)->get()->each(function ($item) {
            $item->hasChildren = $item->hasChildren();
            if (is_array($item->permission)) {
                $item->permission = implode("\n", $item->permission);
            }
        });
        return ['childs' => $list];
    }


    /**
     * @GetApi(summary="查询开放浏览接口",security=true)
     * @Query(key="field")
     * @throws \Exception
     */
    public function getOpenApis()
    {
        $field = $this->request->input('field', 'open_api');
        $conf = Config::query()->where([
                'namespace' => 'system',
                'name' => 'permissions',
            ])->value('value')[$field] ?? [];
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
                    $route = is_string($route) ? rtrim($route) : rtrim($v[0]->route);
                    $route_key = "$http_method::{$route}";
                    if (in_array($route_key, $conf)) {
                        continue;
                    }
                    // 过滤掉脚手架页面配置方法
                    $callback = is_array($v) ? ($v[0]->callback) : $v->callback;
                    if (!is_array($callback)) {
                        if (strpos($callback, '@') !== false) {
                            [$controller, $action] = explode('@', $callback);
                        } else {
                            continue;
                        }
                    } else {
                        [$controller, $action] = $callback;
                    }

                    if (empty($action) || in_array($action, [
                            'Cconf',
                            'dbAct',
                            'tableAct',
                            'transType',
                            'validate',
                            'controller',
                            'tableSchema',
                            'form',
                            'make',
                            'form_update',
                            'updateDetail',
                            'form_create',
                            'maker',
                        ])) {
                        continue;
                    }
                    $options[] = [
                        'id' => $route_key,
                        'controller' => $route,
                        'action' => $action,
                        'http_method' => $http_method,
                    ];
                }
            }
        }
        $right_options = [];
        foreach ($conf as $route) {
            [$http_method, $uri] = explode("::", $route, 2);
            $dispatcher = getContainer(DispatcherFactory::class)->getDispatcher('http');
            $route_info = $dispatcher->dispatch($http_method, $uri);
            if (!empty($route_info[1]->callback[0])) {
                if (is_string($route_info[1]->callback) && strpos($route_info[1]->callback, '@') !== false) {
                    [$action] = explode('@', $route_info[1]->callback);
                } else {
                    $action = $route_info[1]->callback[1];
                }
                $right_options[] = [
                    'id' => $route,
                    'controller' => $route,
                    'action' => $action,
                    'http_method' => $http_method,
                ];
            }
        }

        return $this->json([
            'left' => $options,
            'right' => $right_options,
        ]);
    }

    /**
     * 删除后操作，删除子节点
     */
    public function _delete_after($pk_val, &$data)
    {
        if ($pk_val === null) {
            $pk_val = [$data->id];
        }
        $sub_ids = $this->getModel()->whereIn('pid', $pk_val)->select(['id'])->get()->toArray();
        if ($sub_ids) {
            $sub_ids = array_column($sub_ids, 'id');
            $this->_delete_after($sub_ids, $data);
        }
        if (is_array($pk_val)) {
            $this->getModel()->whereIn('id', $pk_val)->delete();
        }
    }

}
