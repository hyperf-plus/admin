<?php

namespace App\Model;

use App\Exception\AddException;
use App\Exception\RESTException;
use Hyperf\DbConnection\Db;
use mysql_xdevapi\Exception;
use Symfony\Component\Mime\Exception\AddressEncoderException;

class UserLevel extends BaseModel
{

    protected $table = 'user_level';
    protected $primaryKey = 'user_level_id';
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'user_level_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'user_level_id' => 'integer',
        'amount' => 'float',
        'discount' => 'integer',
    ];

    /**
     * 获取一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getLevelItem($data)
    {
        $this->validateData($data, 'UserLevel.item');
        $result = self::find($data['user_level_id']);
        if ($result) {
            return $result->toArray();
        }
        return [];
    }

    /**
     * 获取账号等级列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getLevelList($data)
    {
        $this->validateData($data, 'User.list');
        // 排序方式
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';
        // 排序的字段
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'amount';
        $result = self::orderBy($orderField, $orderType)->get();
        if (false !== $result) {
            return $result->toArray();
        }
        return false;
    }

    /**
     * 添加一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addLevelItem($data)
    {
        $this->validateData($data, 'UserLevel');
        // 避免无关字段
        unset($data['user_level_id']);
        if ($this->forceFill($data)->save()) {
            return $this->toArray();
        }
        return false;
    }

    /**
     * 编辑一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setLevelItem($data)
    {
        $this->validateSetData($data, 'UserLevel.set');
        $result = $this->find($data['user_level_id']);
        if (!$result) {
            throw new RESTException('数据不存在');
        }
        // 开启事务
        try {
            $result->forceFill($data)->save();
            return $result->toArray();
        } catch (\Exception $e) {
            throw new RESTException($e->getMessage());
        }
    }

    /**
     * 批量删除账号等级
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws RESTException
     * @throws \App\Exception\ValidateException
     */
    public function delLevelList($data)
    {
        $this->validateData($data, 'UserLevel.del');
        foreach ($data['user_level_id'] as $item){
            if (User::checkUnique(['user_level_id' => $item])) {
                throw new RESTException('等级已在使用中,建议进行编辑修改');
            }
        }
        self::destroy($data['user_level_id']);
        return true;
    }
}
