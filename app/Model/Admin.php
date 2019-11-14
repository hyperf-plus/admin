<?php
declare(strict_types=1);

namespace App\Model;


use App\Controller\Db;
use App\Exception\RESTException;
use App\Exception\ValidateException;

class Admin extends BaseModel
{

    protected $primaryKey = 'admin_id';

    public $timestamps = true;
    protected $dateFormat = 'U';

    /**
     * 应该被调整为日期的属性
     * @var array
     */
    protected $dates = [
        'create_time',
        'update_time'
    ];

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'password',
        'is_delete',
        'delete_time'
    ];

    protected $table = 'admin';
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'admin_id',
        'username',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'admin_id' => 'integer',
        'group_id' => 'integer',
        'last_login' => 'timestamp',
        'status' => 'integer',
        'is_delete' => 'integer',
        'head_pic' => 'array',
    ];

    /**
     * 密码修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }


    /**
     * hasOne db_token
     * @access public
     * @return mixed
     */
    public function hasToken()
    {
        return $this->hasOne(Token::class, 'client_id', 'admin_id');
    }

    /**
     * hasOne cs_auth_group
     * @access public
     * @return mixed
     */
    public function getAuthGroup()
    {
        return $this->hasOne(AuthGroup::class, 'group_id', 'group_id');
    }

    /**
     * 添加一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     * @throws
     */
    public function addAdminItem($data)
    {
        $this->validateData($data, 'Admin');
        if (false !== $this->fillable(['username', 'password', 'group_id', 'nickname', 'head_pic'])->fill($data)->save()) {
            return $this->setHidden(['password'])->toArray();
        }
        return false;
    }


    /**
     * 登录账号
     * @access public
     * @param array $data 外部数据
     * @param bool $isGetToken 是否需要返回Token
     * @return array|false
     * @throws
     */
    public function loginAdmin($data, $isGetToken = true)
    {

        try {
            $this->validateData($data, 'Admin.login');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }

        // 根据账号获取
        $result = self::where(['username' => $data['username']])->first();
        if (!$result) {
            throw new RESTException('账号不存在');
        }
        if ($result->getAttribute('status') !== 1) {
            throw new RESTException('账号已禁用');

        }
        if (!hash_equals($result->getAttribute('password'), user_md5($data['password']))) {
            return $this->setError('账号或密码错误');
        }
        $data['last_login'] = time();
        $data['last_ip'] = getClientIp();
        $result->fillable(['last_login', 'last_ip'])->fill($data)->save();
        if (!$isGetToken) {
            return ['admin' => $result->toArray()];
        }
        $adminId = $result->getAttribute('admin_id');
        $groupId = $result->getAttribute('group_id');
        $tokenDb = new Token();
        $tokenResult = $tokenDb->setToken($adminId, $groupId, 1, $data['username'], $data['platform']);
        return ['admin' => $result->toArray(), 'token' => $tokenResult];
    }

    /**
     * 重置一个账号密码
     * @access public
     * @param array $data 外部数据
     * @return array
     * @throws RESTException
     * @throws ValidateException
     */
    public function resetAdminItem($data)
    {
        $this->validateData($data, 'Admin.reset');
        // 初始化部分数据
        $data['password'] = mb_strtolower(uuid(8), 'utf-8');
        $map[] = ['admin_id', '=', $data['client_id']];
        $result = $this->where($map)->first();
        if (empty($result)) {
            throw new RESTException('用户不存在');
        }
        if ($result->forceFill(['password' => $data['password']])->save()) {
            //Cache::clear('token:admin_' . $data['client_id']);
            return ['password' => $data['password']];
        }
        throw new RESTException('重置失败');
    }

    /**
     * 批量删除账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAdminList($data)
    {
        $this->validateData($data, 'Admin.del');
        $result = $this->whereIn('admin_id', $data['client_id'])->get();
        if (count($result) == 0) {
            throw new RESTException('用户不存在');
        }
        foreach ($result as $item) {
            $item->forceFill(['is_delete' => 1])->save();
            $item->hasToken()->where(function ($query) use ($data) {
                $query->whereIn('client_id', $data['client_id']);
                $query->where('client_type', 1);
            })->forceDelete();
        }
        return true;
    }


    /**
     * 编辑一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAdminItem($data)
    {

        $this->validateSetData($data, 'Admin.set');
        // 数据类型修改
        $data['client_id'] = (int)$data['client_id'];
        if (!empty($data['nickname'])) {
            $nickMap = [];
            $nickMap[] = ['admin_id', '!=', $data['client_id']];
            $nickMap[] = ['nickname', '=', $data['nickname']];
            if (self::checkUnique($nickMap)) {
                throw new ValidateException('昵称已存在');
            }
        }
        if (isset($data['group_id'])) {
            //TODO::存在组，需要更新权限缓存
            //Cache::clear('token:admin_' . $data['client_id']);
            // $this->hasToken()->where(['client_id' => $data['client_id'], 'client_type' => 1])->delete();
        }
        $map = [];
        $map[] = ['admin_id', '=', $data['client_id']];
        $result = $this->where($map)->first();
        if ($result->fillable(['group_id', 'nickname', 'head_pic'])->fill($data)->save()) {
            return $result->toArray();
        }
        throw new \Exception('保存失败');
    }

    /**
     * 批量设置账号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setAdminStatus($data)
    {
        $this->validateData($data, 'Admin.status');
        $result = self::whereIn('admin_id', $data['client_id'])->get();
        if (count($result) == 0) {
            throw new RESTException('选择的用户不存在！');
        }
        foreach ($result as $item) {
            $item->status = $data['status'];
            $item->save();
            //TODO::存在组，需要更新权限缓存
            // Cache::clear('token:admin_' . $value);
            $item->hasToken()->where('client_type', 1)->delete();
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
    public function setAdminPassword($data)
    {
        if (!$this->validateData($data, 'Admin.change')) {
            return false;
        }

        $result = self::get($data['client_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('账号不存在') : false;
        }

        if (!hash_equals($result->getAttr('password'), user_md5($data['password_old']))) {
            return $this->setError('原始密码错误');
        }

        if (false !== $result->setAttr('password', $data['password'])->save()) {
            Cache::clear('token:admin_' . $data['client_id']);
            $result->hasToken()->where(['client_id' => $data['client_id'], 'client_type' => 1])->delete();
            return true;
        }

        return false;
    }

    /**
     * 获取一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdminItem($data)
    {
        $this->validateData($data, 'Admin.item');

        $result = self::with('getAuthGroup')->first($data['client_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }
        return false;
    }

    /**
     * 获取账号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdminList($data)
    {
        try {
            $this->validateData($data, 'Admin.list');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        // 排序方式
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';
        // 排序的字段
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'admin_id';

        $Result = self::where(function ($query) use ($data) {
            if (isset($data['account']) && !empty($data['account'])) {
                $query->where(function ($query) use ($data) {
                    $query->orWhere('username', 'like', '%' . $data['account'] . '%');
                    $query->orWhere('nickname', $data['account']);
                });
            }
            if (isset($data['group_id']) && !empty($data['group_id'])) {
                $query->where('group_id', $data['group_id']);
            }
            if (isset($data['status']) && !empty($data['status'])) {
                $query->where('status', $data['group_id']);
            }
        })->orderBy($orderField, $orderType)->with('getAuthGroup')->paginate(isset($data['page_size']) ? $data['page_size'] : 20, ['*'], '', isset($data['page_no']) ? $data['page_no'] : 1);

        if (count($Result) <= 0) {
            return ['total_result' => 0];
        } else {
            return ['items' => $Result->items(), 'total_result' => $Result->total()];
        }
    }

    /**
     * 注销账号
     * @access public
     * @return bool
     * @throws
     */
    public function logoutAdmin()
    {
        $map['client_id'] = ['eq', get_client_id()];
        $map['client_type'] = ['eq', 1];

        $token = Request::instance()->param('token');
        if (!empty($token)) {
            $map['token'] = ['eq', $token];
            Cache::rm('token:' . $token);
        }

        $this->hasToken()->where($map)->delete();
        return true;
    }


    /**
     * 刷新Token
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws ValidateException
     */
    public function refreshToken($data)
    {
        if (!$this->validateData($data, 'Admin.refresh')) {
            return false;
        }

        // 获取原始Token
        $oldToken = Request::instance()->param('token', '');

        $tokenDb = new Token();
        $result = $tokenDb->refreshUser(1, $data['refresh'], $oldToken);

        if (false !== $result) {
            Cache::rm('token:' . $oldToken);
            return ['token' => $result];
        }

        return $this->setError($tokenDb->getError());
    }
}
