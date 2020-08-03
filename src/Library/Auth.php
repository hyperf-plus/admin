<?php

namespace Mzh\Admin\Library;


use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Mzh\Admin\Model\AuthRule;
use Mzh\Admin\Model\Menu;

class Auth
{
    private $authMenu = [];
    private $ignore = [];

    public function __construct()
    {
        $this->restart();
    }

    public function check($groupId, $url)
    {
        $accessUrl = $this->getMenuAuth($groupId);
        if (isset($accessUrl[$groupId]['menu']) && in_array(strtolower($url), $accessUrl[$groupId]['menu'], true)) {
            return true;
        }
        return false;
    }

    public function getMenuAuth($groupId = 0, $reload = false)
    {
        if (!empty($this->authMenu) || !$reload) return $this->authMenu;
        $result = AuthRule::query()->where(function ($query) use ($groupId) {
            $query->where('status', 1);
            if ($groupId > 0) $query->where('group_id', $groupId);
        })->get(['menu_auth', 'log_auth', 'group_id'])->toArray();
        $rule = [];
        foreach ($result as $item) {
            //规则
            $group = $item['group_id'];
            $list = Menu::query()->whereIn('menu_id', $item['menu_auth'])->where('url', '!=', '')->get(['url', 'menu_id']);
            $menu = [];
            if (!$list->isEmpty()) {
                $data = [];
                foreach ($list as $menu) {
                    $data[] = strtolower($menu['url']);
                }
                $menu = $data;
            }
            if (!isset($rule[$group]['menu'])) $rule[$group]['menu'] = [];
            $rule[$group]['menu'] = array_merge($rule[$group]['menu'], $menu);
            unset($data, $menu);
            $log_list = Menu::query()->whereIn('menu_id', $item['log_auth'])->where('url', '!=', '')->get(['url', 'menu_id']);
            $menu = [];
            if (!$log_list->isEmpty()) {
                $data = [];
                foreach ($log_list as $menu) {
                    $data[] = strtolower($menu['url']);
                }
                $menu = $data;
            }
            if (!isset($rule[$group]['log'])) $rule[$group]['log'] = [];
            $rule[$group]['log'] = array_merge($rule[$group]['log'], $menu);
            unset($data, $menu);
        }
        $this->authMenu = $rule;
        return $this->authMenu;
    }

    public function setIgnore($url)
    {
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
        $this->getMenuAuth(0, true);
        $security = true;
        foreach ($list->getData() as $meta) {
            foreach ($meta as $key => $routers) {
                foreach ($routers as $url => $router) {
                    list($className, $methodName) = $router->callback;
                    if (empty($className)) {
                        list($className, $methodName) = explode('@', $router->callback);
                    }
                    $metadata = AnnotationCollector::get($className);
                    foreach ($metadata['_m'][$methodName] ?? [] as $item) {
                        if ($item instanceof Mapping) {
                            $security = $item->security;
                            break;
                        }
                    }
                    if (!$security) {
                        #该节点无需验证权限，添加导忽略列表
                        $this->setIgnore($url);
                    }
                }
            }
        }
    }
}