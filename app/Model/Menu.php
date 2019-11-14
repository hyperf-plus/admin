<?php

namespace App\Model;


use App\Exception\AuthRuleException;
use App\Exception\RESTException;
use App\Exception\ValidateException;
use Hyperf\DbConnection\Db;

class Menu extends BaseModel
{

    protected $table = 'menu';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'menu_id',
        'module',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'menu_id' => 'integer',
        'parent_id' => 'integer',
        'type' => 'integer',
        'is_navi' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
    ];

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
        $word = array_map(['think\\helper\\Str', 'snake'], $word);

        return implode('/', $word);
    }

    /**
     * 添加一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addMenuItem($data)
    {
        try {
            $this->validateData($data, 'Menu');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        // 避免无关字段,并且转换格式
        unset($data['menu_id']);
        empty($data['url']) ?: $data['url'] = $this->strToSnake($data['url']);
        if (!empty($data['url']) && 0 == $data['type']) {
            $map['module'] = ['eq', $data['module']];
            $map['type'] = ['eq', 0];
            $map['url'] = ['eq', $data['url']];
            if (self::checkUnique($map)) {
                return $this->setError('Url已存在');
            }
        }

        if (false !== $this->allowField(true)->save($data)) {
            Cache::clear('CommonAuth');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMenuItem($data)
    {
        $this->validateData($data, 'Menu.item');

        $result = self::get($data['menu_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 编辑一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMenuItem($data)
    {
        if (!$this->validateSetData($data, 'Menu.set')) {
            return false;
        }

        $result = self::get($data['menu_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        // 检测编辑后是否存在重复URL
        empty($data['url']) ?: $data['url'] = $this->strToSnake($data['url']);
        isset($data['type']) ?: $data['type'] = $result->getAttr('type');
        isset($data['url']) ?: $data['url'] = $result->getAttr('url');

        if (!empty($data['url']) && 0 == $data['type']) {
            $map['menu_id'] = ['neq', $data['menu_id']];
            $map['module'] = ['eq', $result->getAttr('module')];
            $map['type'] = ['eq', 0];
            $map['url'] = ['eq', $data['url']];

            if (self::checkUnique($map)) {
                return $this->setError('Url已存在');
            }
        }

        // 父菜单不能设置成自身或所属的子菜单
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['menu_id']) {
                return $this->setError('上级菜单不能设为自身');
            }

            $menuList = self::getMenuListData([], $result->getAttr('module'), $data['menu_id']);
            if (false === $menuList) {
                return $this->setError('菜单获取失败');
            }

            foreach ($menuList as $value) {
                if ($data['parent_id'] == $value['menu_id']) {
                    return $this->setError('上级菜单不能设为自身的子菜单');
                }
            }
        }

        if (false !== $result->allowField(true)->save($data)) {
            Cache::clear('CommonAuth');
            return $result->toArray();
        }

        return false;
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
     * @throws
     */
    public static function getMenuListData($menuAuth, $module, $menuId = 0, $isLayer = false, $level = null, $filter = null)
    {
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
//
        $result = Db::table('menu as m')
            ->leftJoin('menu as s', 's.parent_id', '=', 'm.menu_id')
            ->where($map)
            ->groupBy('m.menu_id')
            ->orderBy('m.parent_id')
            ->orderBy('m.sort')
            ->orderBy('m.menu_id')
            ->select(['m.*', Db::raw('count(cs_s.menu_id) as children_total')])
            ->get();
        if (false == $result) {
            throw new AuthRuleException('菜单读取错误');
        }
        $treeCache .= sprintf('id%dlevel%dis_layer%d', $menuId, is_null($level) ? -1 : $level, $isLayer);
        empty($menuAuth) ?: $treeCache .= 'auth' . implode(',', $menuAuth);
        $tree = [];
        $tree = self::setMenuTree($tree, $menuAuth, (int)$menuId, $result, $level, $isLayer);
        if (!empty($menuAuth)) {
            foreach ($result as $value) {
                $value->setAttributes('level', 0);
                $tree[] = $value;
            }
        }
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
            $menuId = $value->menu_id;
            // 优先处理:存在权限列表则需要检测,否则删除节点
            if (!empty($menuAuth) && !in_array($menuId, $menuAuth)) {
                unset($list[$key]);
                continue;
            }

            // 判断菜单是否存在继承关系
            if ($value->parent_id !== $parentId && $menuId !== $parentId) {
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
            $value->level = $level;
            $tree[] = $value;
            // 需要返回本级菜单时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }
            // 删除已使用数据,减少查询次数
            unset($list[$key]);
            if ($value->children_total > 0) {
                self::setMenuTree($tree, [], $menuId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }
        return $tree;
    }

    /**
     * 删除一个菜单(影响下级子菜单)
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function delMenuItem($data)
    {
        if (!$this->validateData($data, 'Menu.del')) {
            return false;
        }

        $result = self::get($data['menu_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        $menuList = self::getMenuListData([], $result->getAttr('module'), $data['menu_id'], true);
        if (false === $menuList) {
            return false;
        }

        $delList = array_column($menuList, 'menu_id');
        self::destroy($delList);
        Cache::clear('CommonAuth');

        return ['children' => $delList];
    }

    /**
     * 根据Id获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuIdNavi($data)
    {
        if (!$this->validateData($data, 'Menu.navi')) {
            return false;
        }

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $filter['is_navi'] = 1;
        $filter['status'] = 1;
        $data['menu_id'] = isset($data['menu_id']) ?: 0;

        return self::getParentList(request()->module(), $data['menu_id'], $isLayer, $filter);
    }

    /**
     * 根据Url获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuUrlNavi($data)
    {
        if (!$this->validateData($data, 'Menu.url')) {
            return false;
        }

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $filter['is_navi'] = 1;
        $filter['status'] = 1;
        $filter['url'] = isset($data['url']) ? $data['url'] : null;

        return self::getParentList(request()->module(), 0, $isLayer, $filter);
    }

    /**
     * 批量设置是否导航
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMenuNavi($data)
    {
        if (!$this->validateData($data, 'Menu.nac')) {
            return false;
        }

        $map['menu_id'] = ['in', $data['menu_id']];
        if (false !== $this->save(['is_navi' => $data['is_navi']], $map)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }

    /**
     * 设置菜单排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMenuSort($data)
    {
        if (!$this->validateData($data, 'Menu.sort')) {
            return false;
        }

        $map['menu_id'] = ['eq', $data['menu_id']];
        if (false !== $this->save(['sort' => $data['sort']], $map)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param  $data
     * @return bool
     * @throws \Exception
     */
    public function setMenuIndex($data)
    {
        if (!$this->validateData($data, 'Menu.index')) {
            return false;
        }

        $list = [];
        foreach ($data['menu_id'] as $key => $value) {
            $list[] = ['menu_id' => $value, 'sort' => $key + 1];
        }

        if (false !== $this->isUpdate()->saveAll($list)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }

    /**
     * 根据编号获取上级菜单列表
     * @access public
     * @param string $module 所属模块
     * @param int $menuId 菜单编号
     * @param bool $isLayer 是否返回本级
     * @param array $filter 过滤'is_navi'与'status'
     * @return array|false
     */
    public static function getParentList($module, $menuId, $isLayer = false, $filter = null)
    {
        // 搜索条件
        $map['module'] = ['eq', $module];

        // 过滤'is_navi'与'status'
        foreach ((array)$filter as $key => $value) {
            if ($key != 'is_navi' && $key != 'status') {
                continue;
            }

            $map[$key] = $value;
        }

        $list = self::cache(true, null, 'CommonAuth')->where($map)->column(null, 'menu_id');
        if ($list === false) {
            Cache::clear('CommonAuth');
            return false;
        }

        // 判断是否根据url获取
        if (isset($filter['url'])) {
            $url = array_column($list, 'menu_id', 'url');
            if (isset($url[$filter['url']])) {
                $menuId = $url[$filter['url']];
                unset($url);
            }
        }

        // 是否返回本级
        if (!$isLayer && isset($list[$menuId])) {
            $menuId = $list[$menuId]['parent_id'];
        }

        $result = [];
        while (true) {
            if (!isset($list[$menuId])) {
                break;
            }

            $result[] = $list[$menuId];

            if ($list[$menuId]['parent_id'] <= 0) {
                break;
            }

            $menuId = $list[$menuId]['parent_id'];
        }

        return array_reverse($result);
    }

    /**
     * 设置菜单状态(影响上下级菜单)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMenuStatus($data)
    {
        if (!$this->validateData($data, 'Menu.status')) {
            return false;
        }

        $result = self::get($data['menu_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        if ($result->getAttr('status') == $data['status']) {
            return $this->setError('状态未改变');
        }

        // 获取当前菜单模块名
        $module = $result->getAttr('module');

        // 如果是启用,则父菜单也需要启用
        $parent = [];
        if ($data['status'] == 1) {
            $parent = self::getParentList($module, $data['menu_id'], false);
            if (false === $parent) {
                return false;
            }
        }

        // 子菜单则无条件继承
        $children = self::getMenuListData([], $module, $data['menu_id'], true);
        if (false === $children) {
            return false;
        }

        $parent = array_column($parent, 'menu_id');
        $children = array_column($children, 'menu_id');

        $map['menu_id'] = ['in', array_merge($parent, $children)];
        $map['status'] = ['eq', $result->getAttr('status')];

        if (false !== $this->save(['status' => $data['status']], $map)) {
            Cache::clear('CommonAuth');
            return ['parent' => $parent, 'children' => $children, 'status' => (int)$data['status']];
        }

        return false;
    }

    /**
     * 获取菜单列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws ValidateException
     */
    public function getMenuList($data)
    {
        if (!$this->validateData($data, 'Menu.list')) {
            return false;
        }

        $menuId = isset($data['menu_id']) ? $data['menu_id'] : 0;
        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $level = isset($data['level']) ? $data['level'] : null;

        $filter = null;
        is_empty_parm($data['is_navi']) ?: $filter['is_navi'] = $data['is_navi'];
        is_empty_parm($data['status']) ?: $filter['status'] = $data['status'];

        return self::getMenuListData([], $data['module'], $menuId, $isLayer, $level, $filter);
    }

    /**
     * 根据权限获取菜单列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws RESTException
     */
    public function getMenuAuthList($data)
    {
        try {
            $this->validateData($data, 'Menu.auth');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        // 获取当前登录账号对应的权限数据
        //get_client_group()
        $ruleResult = AuthRule::getMenuAuthRule($data['module'], 1);
        if (empty($ruleResult['menu_auth'])) {
            return [];
        }
        // 当规则表中存在菜单权限时进行赋值,让获取的函数进行过滤
        $menuAuth = $ruleResult['menu_auth'];
        $menuId = isset($data['menu_id']) ? $data['menu_id'] : 0;
        $result = self::getMenuListData($menuAuth, $data['module'], $menuId, true);
        return $result;
    }

    /**
     * 获取以URL为索引的菜单列表
     * @access public
     * @param string $module 所属模块
     * @param int $status 菜单状态
     * @return array|false
     */
    public static function getUrlMenuList($module, $status = 1)
    {
        // 缓存名称
        $key = 'urlMenuList' . $module . $status;

        $map['module'] = ['eq', $module];
        $map['status'] = ['eq', $status];

        $result = self::cache($key, null, 'CommonAuth')->where($map)->column(null, 'url');
        if (!$result) {
            Cache::rm($key);
            return false;
        }

        return $result;
    }
}
