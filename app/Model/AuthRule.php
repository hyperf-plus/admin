<?php

namespace App\Model;


use App\Exception\AuthRuleException;
use App\Exception\LoginException;
use Hyperf\Database\Model\Model;

class AuthRule extends BaseModel
{

    protected $table = 'auth_rule';
    protected $primaryKey = 'rule_id';
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'rule_id',
        'group_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'rule_id' => 'integer',
        'group_id' => 'integer',
        'menu_auth' => 'array',
        'log_auth' => 'array',
        'sort' => 'integer',
        'status' => 'integer',
    ];


    /**
     * 添加一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAuthRuleItem($data)
    {
        if (!$this->validateData($data, 'AuthRule')) {
            return false;
        }

        // 避免无关字段
        unset($data['rule_id']);
        !empty($data['menu_auth']) ?: $data['menu_auth'] = [];
        !empty($data['log_auth']) ?: $data['log_auth'] = [];

        $map['module'] = ['eq', $data['module']];
        $map['group_id'] = ['eq', $data['group_id']];

        if (self::checkUnique($map)) {
            return $this->setError('当前模块下已存在相同用户组');
        }

        if (false !== $this->allowField(true)->save($data)) {
            Cache::clear('CommonAuth');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthRuleItem($data)
    {
        if (!$this->validateData($data, 'AuthRule.get')) {
            return false;
        }

        $result = self::get($data['rule_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 编辑一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAuthRuleItem($data)
    {
        if (!$this->validateSetData($data, 'AuthRule.set')) {
            return false;
        }

        // 数组字段特殊处理
        if (isset($data['menu_auth']) && '' == $data['menu_auth']) {
            $data['menu_auth'] = [];
        }

        if (isset($data['log_auth']) && '' == $data['log_auth']) {
            $data['log_auth'] = [];
        }

        // 获取原始数据
        $result = self::get($data['rule_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        if (!empty($data['module'])) {
            $map['rule_id'] = ['neq', $data['rule_id']];
            $map['module'] = ['eq', $data['module']];
            $map['group_id'] = ['eq', $result->getAttr('group_id')];

            if (self::checkUnique($map)) {
                return $this->setError('当前模块下已存在相同用户组');
            }
        }

        if (false !== $result->allowField(true)->save($data)) {
            Cache::clear('CommonAuth');
            return $result->toArray();
        }

        return false;
    }

    /**
     * 批量删除规则
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAuthRuleList($data)
    {
        if (!$this->validateData($data, 'AuthRule.del')) {
            return false;
        }

        self::destroy($data['rule_id']);
        Cache::clear('CommonAuth');

        return true;
    }

    /**
     * 获取规则列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthRuleList($data)
    {
        if (!$this->validateData($data, 'AuthRule.list')) {
            return false;
        }

        $result = self::all(function ($query) use ($data) {
            // 搜索条件
            $map = [];
            empty($data['group_id']) ?: $map['group_id'] = ['eq', $data['group_id']];
            is_empty_parm($data['module']) ?: $map['module'] = ['eq', $data['module']];
            is_empty_parm($data['status']) ?: $map['status'] = ['eq', $data['status']];

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'rule_id';

            // 排序处理
            $order['sort'] = 'asc';
            $order[$orderField] = $orderType;

            if (!empty($data['order_field'])) {
                $order = array_reverse($order);
            }

            $query
                ->cache(true, null, 'CommonAuth')
                ->where($map)
                ->order($order);
        });

        if (false === $result) {
            Cache::clear('CommonAuth');
            return false;
        }

        return $result->toArray();
    }

    /**
     * 批量设置规则状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthRuleStatus($data)
    {
        if (!$this->validateData($data, 'AuthRule.status')) {
            return false;
        }

        $map['rule_id'] = ['in', $data['rule_id']];
        if (false !== $this->save(['status' => $data['status']], $map)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }

    /**
     * 设置规则排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthRuleSort($data)
    {
        if (!$this->validateData($data, 'AuthRule.sort')) {
            return false;
        }

        $map['rule_id'] = ['eq', $data['rule_id']];
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
    public function setAuthRuleIndex($data)
    {
        if (!$this->validateData($data, 'AuthRule.index')) {
            return false;
        }

        $list = [];
        foreach ($data['rule_id'] as $key => $value) {
            $list[] = ['rule_id' => $value, 'sort' => $key + 1];
        }

        if (false !== $this->isUpdate()->saveAll($list)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }

    /**
     * 根据用户组编号与对应模块获取权限明细
     * @access public
     * @param string $module 对应模块
     * @param int $groupId 用户组编号
     * @return array|false
     * @throws
     */
    public static function getMenuAuthRule($module, $groupId)
    {
//        // 需要加入游客组的权限(已登录账号也可以使用游客权限)
//        if (AUTH_GUEST !== $groupId) {
//            $groupId = [$groupId, AUTH_GUEST];
//        }
        if (!is_array($groupId)) {
            $groupId = (array)$groupId;
        }
        $result = self::where('module', $module)->where('status', 1)->whereIn('group_id', $groupId)->get();
        if (false === $result) {
            throw new AuthRuleException('暂无节点');
        }
        $menuAuth = [];
        $logAuth = [];
        $whiteList = [];
        $result = $result->toArray();
        foreach ($result as $value) {
            // 默认将所有获取到的编号都归入数组
            if (!empty($value['menu_auth'])) {
                $menuAuth = array_merge($menuAuth, json_decode($value['menu_auth'], true));
//                // 游客组需要将权限加入白名单列表
//                if (AUTH_GUEST == $value['group_id']) {
//                    $whiteList = array_merge($whiteList, $value['menu_auth']);
//                }
            }
            if (!empty($value['log_auth'])) {
                $logAuth = array_merge($logAuth, json_decode($value['log_auth'], true));
            }
        }
        return [
            'menu_auth' => array_unique($menuAuth),
            'log_auth' => array_unique($logAuth),
            'white_list' => $whiteList,
        ];
    }
}
