<?php
declare(strict_types=1);

namespace App\Model;


use App\Exception\RESTException;
use Hyperf\DbConnection\Db;

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
    protected $guarded = [
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
        'user_id' => 'integer',
        'is_mobile' => 'integer',
        'is_email' => 'integer',
        'sex' => 'integer',
        'user_level_id' => 'integer',
        'user_address_id' => 'integer',
        'group_id' => 'integer',
        'last_login' => 'timestamp',
        'status' => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * 密码修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    protected function setPasswordAttr($value)
    {
        return user_md5($value);
    }

    /**
     * 全局查询条件
     * @access protected
     * @param object $query 模型
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
        return $this->hasOne(userMoney::class, 'user_id', 'user_id');
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
            ->hasOne(userLevel::class, 'user_level_id', 'user_level_id')->addSelect(['user_level_id', 'icon', 'name']);
    }

    /**
     * hasOne cs_auth_group
     * @access public
     * @return mixed
     */
    public function getAuthGroup()
    {
        return $this
            ->hasOne(AuthGroup::class, 'group_id', 'group_id')->addSelect(['*']);
    }

    /**
     * 注册一个新账号
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     * @throws
     */
    public function addUserItem($data)
    {
        $this->validateData($data, 'User');
        // 开启事务
        Db::beginTransaction();
        try {
            // 添加主表
            if (!isset($data['group_id'])) {
                $data['group_id'] = AUTH_CLIENT;
            }
            is_client_admin() ?: $data['group_id'] = AUTH_CLIENT;
            //TODO：：图标改为自动获取。防止数据冗余 暂未实现，需要改前端
            // $data['level_icon'] = UserLevel::where('user_level_id', 1)->value('icon', '');
            $field = [
                'password', 'head_pic', 'sex', 'birthday', 'level_icon',
                'username', 'mobile', 'email', 'nickname', 'group_id'
            ];;
            foreach ($data as $key=>$val){
                if (!in_array($key,$field)){
                    unset($data[$key]);
                }
            }
            self::unguard();
            if (!$this->create($data)->hasUserMoney()->create()) {
                throw new RESTException('创建失败');
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw new RESTException($e->getMessage());
        }
    }

    /**
     * 编辑一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setUserItem($data)
    {
        $this->validateData($data, 'User.set');
        // 数据类型修改
        $data['client_id'] = (int)$data['client_id'];
        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $field = ['group_id', 'nickname', 'head_pic', 'sex', 'birthday', 'status'];
        $result = self::find($userId);
        if (!$result) {
            throw new RESTException('账号不存在');
        }
        if (!empty($data['nickname'])) {
            $nickMap = [];
            $nickMap[] = ['user_id', '!=', $userId];
            $nickMap[] = ['nickname', '=', $data['nickname']];
            if (self::checkUnique($nickMap)) {
                throw new RESTException('昵称已存在');
            }
        }
        if (!is_client_admin()) {
            unset($data['password']);
            unset($data['status']);
            unset($data['group_id']);
        } else {
            if (!empty($data['password']) || isset($data['group_id'])) {
                array_push($field, 'password');
                //TODO:: 清理用户登录TOKEN缓存 CacheClear('token:user_' . $value);
                //   Cache::clear('token:user_' . $map['user_id']);
                //  $this->hasToken()->where(['client_id' => $map['user_id'], 'client_type' => 0])->delete();
            }
        }
        if ($result->fillable($field)->fill($data)->save()) {
            return $this->toArray();
        }
        return [];
    }

    /**
     * 批量设置账号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setUserStatus($data)
    {
        $this->validateData($data, 'User.status');
        $idList = is_client_admin() ? $data['client_id'] : [0];
        $result = $this->whereIn('user_id', $idList)->get();
        foreach ($result as $item) {
            $item->status = $data['status'];
            $item->save();
            //TODO:: 清理用户登录TOKEN缓存 CacheClear('token:user_' . $value);
        }
        return true;
    }

    /**
     * 修改一个账号密码
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setUserPassword($data)
    {
        $this->validateData($data, 'User.change');
        // 获取实际账号Id
        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $result = self::find($userId);
        if (!$result) {
            throw new RESTException('账号不存在');
        }
        if (!is_client_admin()) {
            // 获取账号数据
            if (empty($data['password_old'])) {
                throw new RESTException('原始密码不能为空');
            }
            if (!hash_equals($result->getAttribute('password'), user_md5($data['password_old']))) {
                throw new RESTException('原始密码错误');
            }
        }

        $result->password = $data['password'];
        $result->save();
        //TODO:: 清理用户登录TOKEN缓存 CacheClear('token:user_' . $value);
        //Cache::clear('token:user_' . $userId);
        // $this->hasToken()->where(['client_id' => $userId, 'client_type' => 0])->delete();
        return true;
    }

    /**
     * 批量删除账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delUserList($data)
    {
        $this->validateData($data, 'User.del');
        $idList = is_client_admin() ? $data['client_id'] : [0];
        $result = $this->whereIn('user_id', $idList)->get();
        foreach ($result as $item) {
            $item->is_delete = 1;
            $item->save();
            //TODO:: 清理用户登录TOKEN缓存 CacheClear('token:user_' . $value);
        }
        // $this->hasToken()->where(['client_id' => ['in', $idList], 'client_type' => 0])->delete();
        return true;
    }

    /**
     * 获取一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserItem($data)
    {
        $this->validateData($data, 'User.item');
        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $result = self::find($userId, ['getUserLevel', 'getAuthGroup']);
        return $result->toArray();
    }

    /**
     * 获取一个账号的简易信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserInfo($data)
    {
        $this->validateData($data, 'User.item');
        $result = self::find(is_client_admin() ? $data['client_id'] : get_client_id());
        return $result->toArray();;
    }

    /**
     * 获取账号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserList($data)
    {
        $this->validateData($data, 'User.list');
        // 搜索条件
        $db = self::where(function ($query) use ($data) {
            if (!empty($data['account'])) {
                $query->where(function ($db) use ($data) {
                    $db->where('user.username', 'like', '%' . $data['account'] . '%');
                    $db->where('user.mobile', 'like', '%' . $data['account'] . '%');
                    $db->where('user.nickname', 'like', '%' . $data['account'] . '%');
                });
            }
            is_empty_parm($data['user_level_id']) ?: $query->where('user.user_level_id', $data['user_level_id']);
            is_empty_parm($data['group_id']) ?: $query->where('user.group_id', $data['group_id']);
            is_empty_parm($data['status']) ?: $query->where('user.status', $data['status']);
        });
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
                    $orderField = 'user.' . $data['order_field'];
                    break;
                case 'name':
                case 'discount':
                    $orderField = 'getUserLevel.' . $data['order_field'];
                    break;
            }
        }
        $result = $db->orderBy($orderField, $orderType)->with('getAuthGroup')->with('getUserLevel')->paginate(isset($data['page_size']) ? $data['page_size'] : 20, ['*'], '', isset($data['page_no']) ? $data['page_no'] : 1);
        if ($result) {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
        return ['items' => [], 'total_result' => 0];
    }

    /**
     * 注销账号
     * @access public
     * @return bool
     * @throws
     */
    public function logoutUser()
    {

        // Token 失效
//        $map['client_id'] = ['eq', get_client_id()];
//        $map['client_type'] = ['eq', 0];
//
//        $token = Request::instance()->param('token');
//        if (!empty($token)) {
//            $map['token'] = ['eq', $token];
//            Cache::rm('token:' . $token);
//        }
//
//        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 登录账号
     * @access public
     * @param array $data 外部数据
     * @param bool $isGetToken 是否需要返回Token
     * @return array|false
     * @throws
     */
    public function loginUser($data, $isGetToken = true)
    {
        $this->validateData($data, 'User.login');
        // 根据账号获取
        $result = self::where(['username' => $data['username']])->first();
        if (!$result) {
            return is_null($result) ? $this->setError('账号不存在') : false;
        }
        if ($result->getAttribute('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if (!hash_equals($result->getAttribute('password'), user_md5($data['password']))) {
            return $this->setError('账号或密码错误');
        }

        $data['last_login'] = time();
        $data['last_ip'] = getClientIp();
        unset($data['user_id']);
        $result->fill(['last_login', 'last_ip'])->fillable($data);

        if (!$isGetToken) {
            return ['user' => $result->toArray()];
        }

        $userId = $result->getAttribute('user_id');
        $groupId = $result->getAttribute('group_id');

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
     * @param array $data 外部数据
     * @return array|false
     * @throws \App\Exception\ValidateException
     */
    public function refreshToken($data)
    {
        $this->validateData($data, 'User.refresh');

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
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function findUserPassword($data)
    {
        $this->validateData($data, 'User.find');

        $result = self::where(['username' => $data['username']])->first();
        if (!$result) {
            return is_null($result) ? $this->setError('账号不存在') : false;
        }

        if ($result->getAttribute('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if ($result->getAttribute('mobile') != $data['mobile']) {
            return $this->setError('手机号码错误');
        }

//        // 验证验证码
//        $verifyDb = new Verification();
//        if (!$verifyDb->verVerification($data['mobile'], $data['code'])) {
//            return $this->setError($verifyDb->getError());
//        }

        if (false !== $result->forceFill(['password' => $data['password']])->save()) {
            Cache::clear('token:user_' . $result->getAttr('user_id'));
            $this->hasToken()->where(['client_id' => $result->getAttr('user_id'), 'client_type' => 0])->delete();
            //$verifyDb->useVerificationItem(['number' => $data['mobile']]);
            return true;
        }

        return false;
    }
}
