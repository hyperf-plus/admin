<?php
declare(strict_types=1);

namespace App\Model;


class User extends BaseModel
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'password',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'user_id',
        'username',
        'mobile',
        'email'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'user_id'         => 'integer',
        'is_mobile'       => 'integer',
        'is_email'        => 'integer',
        'sex'             => 'integer',
        'user_level_id'   => 'integer',
        'user_address_id' => 'integer',
        'group_id'        => 'integer',
        'last_login'      => 'timestamp',
        'status'          => 'integer',
        'is_delete'       => 'integer',
    ];

    /**
     * 密码修改器
     * @access protected
     * @param  string $value 值
     * @return string
     */
    protected function setPasswordAttr($value)
    {
        return user_md5($value);
    }

    /**
     * 全局查询条件
     * @access protected
     * @param  object $query 模型
     * @return void
     */
    protected function base($query)
    {
        $query->where(['is_delete' => ['eq', 0]]);
    }

    /**
     * hasOne db_token
     * @access public
     * @return mixed
     */
    public function hasToken()
    {
        return $this->hasOne('Token', 'user_id', 'client_id');
    }

    /**
     * hasOne cs_user_money
     * @access public
     * @return mixed
     */
    public function hasUserMoney()
    {
        return $this->hasOne('userMoney');
    }

    /**
     * hasOne cs_user_money
     * @access public
     * @return mixed
     */
    public function getUserMoney()
    {
        return $this
            ->hasOne('userMoney')
            ->field('total_money,balance,lock_balance,points,lock_points')
            ->setEagerlyType(0);
    }

    /**
     * hasOne cs_user_level
     * @access public
     * @return mixed
     */
    public function getUserLevel()
    {
        return $this
            ->hasOne('userLevel', 'user_level_id', 'user_level_id', [], 'left')
            ->field('name,icon,discount')
            ->setEagerlyType(0);
    }

    /**
     * hasOne cs_auth_group
     * @access public
     * @return mixed
     */
    public function getAuthGroup()
    {
        return $this
            ->hasOne('AuthGroup', 'group_id', 'group_id', [], 'left')
            ->field('name,status')
            ->setEagerlyType(0);
    }

    /**
     * 注册一个新账号
     * @access public
     * @param  array $data 外部数据
     * @return array|bool
     * @throws
     */
    public function addUserItem($data)
    {
        if (!$this->validateData($data, 'User')) {
            return false;
        }

        // 开启事务
        self::startTrans();

        try {
            // 添加主表
            if (!isset($data['group_id'])) {
                $data['group_id'] = AUTH_CLIENT;
            }

            is_client_admin() ?: $data['group_id'] = AUTH_CLIENT;
            $data['level_icon'] = UserLevel::where('user_level_id', 1)->value('icon', '');

            $field = [
                'password', 'head_pic', 'sex', 'birthday', 'level_icon',
                'username', 'mobile', 'email', 'nickname', 'group_id'
            ];

            if (!$this->allowField($field)->save($data)) {
                throw new \Exception($this->getError());
            }

            // 添加资金表
            if (!$this->hasUserMoney()->save([])) {
                throw new \Exception($this->getError());
            }

            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个账号
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setUserItem($data)
    {
        if (!$this->validateData($data, 'User.set')) {
            return false;
        }

        // 数据类型修改
        $data['client_id'] = (int)$data['client_id'];

        $map = ['user_id' => is_client_admin() ? $data['client_id'] : get_client_id()];
        $field = ['group_id', 'nickname', 'head_pic', 'sex', 'birthday', 'status'];

        if (!empty($data['nickname'])) {
            $nickMap['user_id'] = ['neq', $map['user_id']];
            $nickMap['nickname'] = ['eq', $data['nickname']];

            if (self::checkUnique($nickMap)) {
                return $this->setError('昵称已存在');
            }
        }

        if (!is_client_admin()) {
            unset($data['password']);
            unset($data['status']);
            unset($data['group_id']);
        } else {
            if (!empty($data['password']) || isset($data['group_id'])) {
                array_push($field, 'password');
                Cache::clear('token:user_' . $map['user_id']);
                $this->hasToken()->where(['client_id' => $map['user_id'], 'client_type' => 0])->delete();
            }
        }

        if (false !== $this->allowField($field)->save($data, $map)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 批量设置账号状态
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function setUserStatus($data)
    {
        if (!$this->validateData($data, 'User.status')) {
            return false;
        }

        $idList = is_client_admin() ? $data['client_id'] : [0];
        $map['user_id'] = ['in', $idList];

        if (false !== $this->save(['status' => $data['status']], $map)) {
            foreach ($idList as $value) {
                Cache::clear('token:user_' . $value);
            }

            $this->hasToken()->where(['client_id' => ['in', $idList], 'client_type' => 0])->delete();
            return true;
        }

        return false;
    }

    /**
     * 修改一个账号密码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function setUserPassword($data)
    {
        if (!$this->validateData($data, 'User.change')) {
            return false;
        }

        // 获取实际账号Id
        $userId = is_client_admin() ? $data['client_id'] : get_client_id();

        if (!is_client_admin()) {
            // 获取账号数据
            $result = self::get($userId);
            if (!$result) {
                return is_null($result) ? $this->setError('账号不存在') : false;
            }

            if (empty($data['password_old'])) {
                return $this->setError('原始密码不能为空');
            }

            if (!hash_equals($result->getAttr('password'), user_md5($data['password_old']))) {
                return $this->setError('原始密码错误');
            }
        }

        if (false !== $this->save(['password' => $data['password']], ['user_id' => ['eq', $userId]])) {
            Cache::clear('token:user_' . $userId);
            $this->hasToken()->where(['client_id' => $userId, 'client_type' => 0])->delete();
            return true;
        }

        return false;
    }

    /**
     * 批量删除账号
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function delUserList($data)
    {
        if (!$this->validateData($data, 'User.del')) {
            return false;
        }

        $idList = is_client_admin() ? $data['client_id'] : [0];
        $map['user_id'] = ['in', $idList];

        if (false !== $this->save(['is_delete' => 1], $map)) {
            foreach ($idList as $value) {
                Cache::clear('token:user_' . $value);
            }

            $this->hasToken()->where(['client_id' => ['in', $idList], 'client_type' => 0])->delete();
            return true;
        }

        return false;
    }

    /**
     * 获取一个账号
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserItem($data)
    {
        if (!$this->validateData($data, 'User.item')) {
            return false;
        }

        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $result = self::get($userId, 'getUserLevel,getAuthGroup');

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个账号的简易信息
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserInfo($data)
    {
        if (!$this->validateData($data, 'User.item')) {
            return false;
        }

        $result = self::get(is_client_admin() ? $data['client_id'] : get_client_id());
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取账号列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserList($data)
    {
        if (!$this->validateData($data, 'User.list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['account']) ?: $map['user.username|user.mobile|user.nickname'] = ['eq', $data['account']];
        is_empty_parm($data['user_level_id']) ?: $map['user.user_level_id'] = ['eq', $data['user_level_id']];
        is_empty_parm($data['group_id']) ?: $map['user.group_id'] = ['eq', $data['group_id']];
        is_empty_parm($data['status']) ?: $map['user.status'] = ['eq', $data['status']];

        $totalResult = $this->with('getUserLevel,getAuthGroup')->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $map) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = 'user.user_id';
            if (!is_empty_parm($data['order_field'])) {
                switch ($data['order_field']) {
                    case 'user_id':
                    case 'username':
                    case 'mobile':
                    case 'nickname':
                    case 'group_id':
                    case 'sex':
                    case 'birthday':
                    case 'user_level_id':
                    case 'status':
                    case 'create_time':
                        $orderField = 'user.'.$data['order_field'];
                        break;

                    case 'name':
                    case 'discount':
                        $orderField = 'getUserLevel.'.$data['order_field'];
                        break;
                }
            }

            $query
                ->with('getUserLevel,getAuthGroup')
                ->where($map)
                ->order([$orderField => $orderType])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 注销账号
     * @access public
     * @return bool
     * @throws
     */
    public function logoutUser()
    {
        $map['client_id'] = ['eq', get_client_id()];
        $map['client_type'] = ['eq', 0];

        $token = Request::instance()->param('token');
        if (!empty($token)) {
            $map['token'] = ['eq', $token];
            Cache::rm('token:' . $token);
        }

        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 登录账号
     * @access public
     * @param  array $data       外部数据
     * @param  bool  $isGetToken 是否需要返回Token
     * @return array|false
     * @throws
     */
    public function loginUser($data, $isGetToken = true)
    {
        if (!$this->validateData($data, 'User.login')) {
            return false;
        }

        // 根据账号获取
        $result = self::get(['username' => $data['username']]);
        if (!$result) {
            return is_null($result) ? $this->setError('账号不存在') : false;
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if (!hash_equals($result->getAttr('password'), user_md5($data['password']))) {
            return $this->setError('账号或密码错误');
        }

        $data['last_login'] = time();
        $data['last_ip'] = Request::instance()->ip();
        unset($data['user_id']);
        $this->allowField(['last_login', 'last_ip'])->save($data, ['username' => $data['username']]);

        if (!$isGetToken) {
            return ['user' => $result->toArray()];
        }

        $userId = $result->getAttr('user_id');
        $groupId = $result->getAttr('group_id');

        $tokenDb = new Token();
        $tokenResult = $tokenDb->setToken($userId, $groupId, 0, $data['username'], $data['platform']);

        if (false === $tokenResult) {
            return $this->setError($tokenDb->getError());
        }

        Cache::clear('token:user_' . $result->getAttr('user_id'));
        return ['user' => $result->toArray(), 'token' => $tokenResult];
    }

    /**
     * 刷新Token
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     */
    public function refreshToken($data)
    {
        if (!$this->validateData($data, 'User.refresh')) {
            return false;
        }

        // 获取原始Token
        $oldToken = Request::instance()->param('token', '');

        $tokenDb = new Token();
        $result = $tokenDb->refreshUser(0, $data['refresh'], $oldToken);

        if (false !== $result) {
            Cache::rm('token:' . $oldToken);
            return ['token' => $result];
        }

        return $this->setError($tokenDb->getError());
    }

    /**
     * 忘记密码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function findUserPassword($data)
    {
        if (!$this->validateData($data, 'User.find')) {
            return false;
        }

        $result = self::get(['username' => $data['username']]);
        if (!$result) {
            return is_null($result) ? $this->setError('账号不存在') : false;
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if ($result->getAttr('mobile') != $data['mobile']) {
            return $this->setError('手机号码错误');
        }

//        // 验证验证码
//        $verifyDb = new Verification();
//        if (!$verifyDb->verVerification($data['mobile'], $data['code'])) {
//            return $this->setError($verifyDb->getError());
//        }

        if (false !== $result->save(['password' => $data['password']])) {
            Cache::clear('token:user_' . $result->getAttr('user_id'));
            $this->hasToken()->where(['client_id' => $result->getAttr('user_id'), 'client_type' => 0])->delete();
            //$verifyDb->useVerificationItem(['number' => $data['mobile']]);
            return true;
        }

        return false;
    }
}
