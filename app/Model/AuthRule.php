<?php

namespace App\Model;


use App\Exception\AuthRuleException;
use App\Exception\LoginException;
use App\Exception\RESTException;
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

    protected $hidden = [
        'is_delete'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
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
        $this->validateData($data, 'AuthRule');
        // 避免无关字段
        unset($data['rule_id']);
        !empty($data['menu_auth']) ?: $data['menu_auth'] = [];
        !empty($data['log_auth']) ?: $data['log_auth'] = [];

        $map['module'] = $data['module'];
        $map['group_id'] = $data['group_id'];

        if (self::checkUnique($map)) {
            return $this->setError('当前模块下已存在相同用户组');
        }
        if ($this->forceFill($data)->save()) {
            CacheCLear('CommonAuth');
            return $this->toArray();
        }
        return [];
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
        $this->validateData($data, 'AuthRule.get');
        $result = self::find($data['rule_id']);
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
        $this->validateSetData($data, 'AuthRule.set');
        // 数组字段特殊处理
        if (isset($data['menu_auth']) && '' == $data['menu_auth']) {
            $data['menu_auth'] = [];
        }

        if (isset($data['log_auth']) && '' == $data['log_auth']) {
            $data['log_auth'] = [];
        }

        // 获取原始数据
        $result = self::find($data['rule_id']);
        if (!$result) {
            throw new RESTException('数据不存在');
        }

        if (!empty($data['module'])) {
            $map = [];
            $map[] = ['rule_id', '!=', $data['rule_id']];
            $map[] = ['module', '=', $data['module']];
            $map[] = ['group_id', '=', $data['group_id']];
            if (self::checkUnique($map)) {
                throw new RESTException('当前模块下已存在相同用户组');
            }
        }

        if ($result->forceFill($data)->save()) {
            CacheClear('CommonAuth');
            return $result->toArray();
        }
        throw new RESTException('保存失败');
    }

    /**
     * 批量删除规则
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws \App\Exception\ValidateException
     */
    public function delAuthRuleList($data)
    {
        $this->validateData($data, 'AuthRule.del');
        self::destroy($data['rule_id']);
        CacheClear('CommonAuth');
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
        $this->validateData($data, 'AuthRule.list');
        $db = self::where(function ($query) use ($data) {
            // 搜索条件
            empty($data['group_id']) ?: $query->where('group_id', $data['group_id']);
            is_empty_parm($data['module']) ?: $query->where('module', $data['module']);
            is_empty_parm($data['status']) ?: $query->where('status', $data['status']);

        });
        // 排序方式
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';
        // 排序的字段
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'rule_id';
        // 排序处理
        $order = [];
        $order['sort'] = 'asc';
        $order[$orderField] = $orderType;
        if (!empty($data['order_field'])) {
            $order = array_reverse($order);
        }
        foreach ($order as $sort => $type) {
            $db->orderBy($sort, $type);
        }
        return $db->get()->toArray();
    }

    /**
     * 批量设置规则状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws \App\Exception\ValidateException
     */
    public function setAuthRuleStatus($data)
    {
        $this->validateData($data, 'AuthRule.status');
        $result = self::whereIn('rule_id', $data['rule_id'])->get();

        foreach ($result as $item) {
            //TODO:: 这里要判断用户是否有操作该组的权限
            $item->status = $data['status'];
            $item->save();
        }
        CacheClear('CommonAuth');
        return true;
    }

    /**
     * 设置规则排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws \App\Exception\ValidateException
     */
    public function setAuthRuleSort($data)
    {
        $this->validateData($data, 'AuthRule.sort');
        $result = self::find($data['rule_id']);
        $result->sort = $data['sort'];
        if ($result->save()) {
            CacheClear('CommonAuth');
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
        $this->validateData($data, 'AuthRule.index');
        foreach ($data['rule_id'] as $key => $rule_id) {
            //TODO::这里要检测是否有此条数据的权限
            $item = self::find($rule_id);
            $item->sort = $key + 1;
            $item->save();
        }
        CacheClear('CommonAuth');
        return true;
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
                $menuAuth = array_merge($menuAuth, (array)$value['menu_auth']);
//                // 游客组需要将权限加入白名单列表
//                if (AUTH_GUEST == $value['group_id']) {
//                    $whiteList = array_merge($whiteList, $value['menu_auth']);
//                }
            }
            if (!empty($value['log_auth'])) {
                $logAuth = array_merge($logAuth, (array)$value['log_auth']);
            }
        }
        return [
            'menu_auth' => array_unique($menuAuth),
            'log_auth' => array_unique($logAuth),
            'white_list' => $whiteList,
        ];
    }
}
