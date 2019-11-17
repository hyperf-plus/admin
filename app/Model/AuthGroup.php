<?php
declare(strict_types=1);

namespace App\Model;

use App\Exception\RESTException;
use App\Exception\ValidateException;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Db;
use mysql_xdevapi\Exception;

class AuthGroup extends BaseModel
{
    use SoftDeletes;
    protected $table = 'auth_group';
    protected $primaryKey = 'group_id';
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'group_id',
        'system',
    ];

    protected $hidden = [
        'is_delete',
        'update_time'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'group_id' => 'integer',
        'system' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * hasMany cs_auth_rule
     * @access public
     * @return mixed
     */
    public function hasAuthRule()
    {
        return $this->hasMany(AuthRule::class, 'group_id');
    }

    /**
     * 添加一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAuthGroupItem($data)
    {
        $this->validateData($data, 'AuthGroup');
        // 避免无关字段
        unset($data['group_id'], $data['system']);
        if ($this->forceFill($data)->save()) {
            CacheClear('CommonAuth');
            return $this->toArray();
        }
        return false;
    }

    /**
     * 编辑一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAuthGroupItem($data)
    {
        try {
            $this->validateSetData($data, 'AuthGroup.set');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        $result = self::find($data['group_id']);
        if (empty($result)) {
            throw new RESTException('用户组不存在！');
        }
        if ($result->guard(['group_id'])->fill($data)->save()) {
            //TODO:: CommonAuth Cache::clear('CommonAuth');
            CacheClear('CommonAuth');
            return $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthGroupItem($data)
    {
        if (!$this->validateData($data, 'AuthGroup.item')) {
            return false;
        }

        $result = self::get($data['group_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 删除一个用户组
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAuthGroupItem($data)
    {
        try {
            $this->validateData($data, 'AuthGroup.del');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        $result = self::find($data['group_id']);
        if (empty($result)) {
            throw new RESTException('数据不存在');
        }
        if ($result->getAttribute('system') === 1) {
            throw new RESTException('系统保留用户组不允许删除');
        }

        // 查询是否已被使用
        if (User::checkUnique(['group_id' => $data['group_id']])) {
            throw new RESTException('当前用户组已被使用');
        }

        if (Admin::checkUnique(['group_id' => $data['group_id']])) {
            throw new RESTException('当前用户组已被使用');
        }
        // 删除本身与规则表中的数据
        $result->delete();
        $result->hasAuthRule()->delete();
        //TODO:: CommonAuth  Cache::clear('CommonAuth');
        CacheClear('CommonAuth');
        return true;
    }

    /**
     * 获取用户组列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthGroupList($data)
    {
        try {
            $this->validateData($data, 'AuthGroup.list');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        // 排序方式
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';
        // 排序的字段
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'group_id';
        $result = self::where(function ($query) use ($data) {
            // 搜索条件
            $map = [];
            if (isset($data['exclude_id'])) {
                $query->whereNotIn('group_id', $data['exclude_id']);
            }
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
            $query->where($map);
        })->orderBy($orderField, $orderType)->get();
        if (false == $result) {
            throw new Exception('查询失败！');
        }
        return $result->toArray();
    }

    /**
     * 批量设置用户组状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthGroupStatus($data)
    {
        try {
            $this->validateData($data, 'AuthGroup.status');
        } catch (ValidateException $e) {
            throw new RESTException($e->getMessage());
        }
        $result = self::whereIn('group_id', $data['group_id'])->get();
        foreach ($result as $item) {
            $item->status = $data['status'];
            $item->save();
        }
        CacheClear('CommonAuth');
        // TODO:: CommonAuth  Cache::clear('CommonAuth');
        return true;
    }

    /**
     * 设置用户组排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthGroupSort($data)
    {
        if (!$this->validateData($data, 'AuthGroup.sort')) {
            return false;
        }

        $map['group_id'] = ['eq', $data['group_id']];
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
    public function setAuthGroupIndex($data)
    {
        if (!$this->validateData($data, 'AuthGroup.index')) {
            return false;
        }

        $list = [];
        foreach ($data['group_id'] as $key => $value) {
            $list[] = ['group_id' => $value, 'sort' => $key + 1];
        }

        if (false !== $this->isUpdate()->saveAll($list)) {
            Cache::clear('CommonAuth');
            return true;
        }

        return false;
    }
}
