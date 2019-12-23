<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\AuthRule;
use App\Model\Menu;
use Exception;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Str;
use Mzh\JwtAuth\Annotations\AuthUpEvict;
use Mzh\Validate\Annotations\Validation;

class MenuService
{
    /**
     * @Validation(mode="AuthRule",field="data")
     * @param array $data
     * @return array
     */
    public function addAuthRuleItem(array $data)
    {
        return $data;
    }

    /**
     * @Validation(mode="Menu",scene="auth",field="data")
     * @param array $data
     * @param array $groupIds
     * @return array
     * @throws Exception
     */
    public function auth(array $data, array $groupIds = [])
    {
        if (empty($groupIds)) {
            //如果没有传需要获取的组ID 则获取当前登录用户的权限信息
            $groupIds = [getUserInfo()->getGroupId()];
        }

        $data['module'] ??= 'admin';

        // 获取当前登录账号对应的权限数据
        $ruleResult = AuthRule::getMenuAuthRule($data['module'], $groupIds);
        if (empty($ruleResult['menu_auth'])) {
            return [];
        }
        // 当规则表中存在菜单权限时进行赋值,让获取的函数进行过滤
        $menuAuth = $ruleResult['menu_auth'];
        $menuId = isset($data['menu_id']) ? $data['menu_id'] : 0;
        return self::getMenuListData($menuAuth, $data['module'], $menuId, true);
    }

    /**
     * 根据条件获取菜单列表数据
     * @access public static
     * @param string $module 所属模块
     * @param int $menuId 菜单Id
     * @param bool $isLayer 是否返回本级菜单
     * @param int $level 菜单深度
     * @param array $filter 过滤'is_navi'与'status'
     * @return array|false
     * @throws Exception
     */
    public static function getMenuListData($menuAuth, $module, $menuId = 0, $isLayer = false, $level = null, $filter = null)
    {
        //进行内存缓存  经测试，缓存redis耗时130多ms  缓存内存只需几毫秒内，
        static $menu = [];
        $exp_time = 60;
        // 缓存名称
        $treeCache = 'MenuTree:' . $module;
        // 搜索条件
        $joinMap = '';
        $map = [];
        $map['m.module'] = $module;
        // 过滤'is_navi'与'status'
        foreach ((array)$filter as $key => $value) {
            if ($key != 'is_navi' && $key != 'status') {
                continue;
            }
            $map['m.' . $key] = (int)$value;
            $joinMap .= sprintf(' AND s.%s = %d', $key, $value);
            $treeCache .= $key . $value;
        }
        $treeCache .= sprintf('id%dlevel%dis_layer%d', $menuId, is_null($level) ? -1 : $level, $isLayer);
        if (!empty($menuAuth)) $treeCache .= 'auth' . implode(',', $menuAuth);

        if (isset($menu[$treeCache]) && $menu[$treeCache]['time'] - time() > 0) {
            return $menu[$treeCache]['data'];
        }

        $result = Menu::query()->from('menu as m')
            ->leftJoin('menu as s', 's.parent_id', '=', 'm.menu_id')
            ->where($map)
            ->groupBy('m.menu_id')
            ->orderBy('m.parent_id')
            ->orderBy('m.sort')
            ->orderBy('m.menu_id')
            ->select(['m.*', Db::raw('count(cs_s.menu_id) as children_total')])
            ->get()->toArray();
        if (0 == count($result)) {
            return [];
        }
        $tree = [];
        $tree = self::setMenuTree($tree, $menuAuth, (int)$menuId, $result, $level, $isLayer);
        if (!empty($menuAuth)) {
            foreach ($result as $value) {
                $value->setAttributes('level', 0);
                $tree[] = $value;
            }
        }
        $menu = null;
        $menu = [];
        $menu[$treeCache] = [];
        $menu[$treeCache]['data'] = $tree;
        $menu[$treeCache]['time'] = (time() + $exp_time);
        return $tree;
    }

    /**
     * 过滤和排序所有菜单
     * @access private
     * @param int $parentId 上级菜单Id
     * @param object $list 原始模型对象
     * @param int $limitLevel 显示多少级深度 null:全部
     * @param bool $isLayer 是否返回本级菜单
     * @param int $level 层级深度
     * @return array
     */
    private static function setMenuTree(&$tree, $menuAuth, $parentId, &$list, $limitLevel = null, $isLayer = false, $level = 0)
    {
        $parentId != 0 ?: $isLayer = false; // 返回全部菜单不需要本级
        foreach ($list as $key => $value) {

            // 获取菜单主Id
            $menuId = $value['menu_id'];
            // 优先处理:存在权限列表则需要检测,否则删除节点
            if (!empty($menuAuth) && !in_array($menuId, $menuAuth)) {
                unset($list[$key]);
                continue;
            }

            // 判断菜单是否存在继承关系
            if ($value['parent_id'] !== $parentId && $menuId !== $parentId) {
                continue;
            }

            // 是否返回本级菜单
            if ($menuId === $parentId && !$isLayer) {
                continue;
            }
            // 限制菜单显示深度
            if (!is_null($limitLevel) && $level > $limitLevel) {
                break;
            }
            $value['level'] = $level;
            $tree[] = $value;
            // 需要返回本级菜单时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }
            // 删除已使用数据,减少查询次数
            unset($list[$key]);
            if ($value['children_total'] > 0) {
                self::setMenuTree($tree, [], $menuId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }
        return $tree;
    }

    /**
     * @Validation(mode="Menu",scene="list",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function list(array $data)
    {
        $data['menu_id'] ??= 0;
        $data['is_layer'] ??= true;
        $data['level'] ??= null;
        return self::getMenuListData([], $data['module'], $data['menu_id'], $data['is_layer'], $data['level'], $data);
    }

    /**
     * @Validation(mode="Menu",scene="index",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update_index(array $data)
    {
        $result = Menu::query(true)->find($data['menu_id']);
        if (count($result) <= 0) {
            throw new Exception('数据不存在');
        }
        $list = [];
        foreach ($result as $key => $item) {
            $item->sort = $key + 1;
            $item->save();
            $list[] = ['menu_id' => $item->menu_id, 'sort' => $key + 1];
        }
        return $list;
    }

    /**
     * @Validation(mode="Menu",filter=true)
     * @AuthUpEvict()
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function create(array $data)
    {
        $Menu = Menu::query(true)->create($data);
        if ($Menu) {
            return $Menu->toArray();
        }
        throw new Exception('添加失败！');
    }

    /**
     * URL驼峰转下划线修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    private function strToSnake($value)
    {
        if (empty($value) || !is_string($value)) {
            return $value;
        }
        $word = explode('/', $value);
        $word = array_map([Str::class, 'snake'], $word);
        return implode('/', $word);
    }

    /**
     * @Validation(mode="Menu",scene="set",field="data",filter=true)
     * @AuthUpEvict()
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update(array $data)
    {
        $result = Menu::query(true)->find($data['menu_id']);
        if (!$result) {
            throw new Exception('数据不存在');
        }
        // 父菜单不能设置成自身或所属的子菜单
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['menu_id']) {
                throw new Exception('上级菜单不能设为自身');
            }
            $menuList = self::getMenuListData([], $result->getAttribute('module'), $data['menu_id']);
            foreach ($menuList as $value) {
                if ($data['parent_id'] == $value['menu_id']) {
                    throw new Exception('上级菜单不能设为自身的子菜单');
                }
            }
        }
        if ($result->fill($data)->save()) {
            return $result->toArray();
        }
        throw new Exception('修改失败');
    }
}
