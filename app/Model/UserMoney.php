<?php

namespace App\Model;

use App\Exception\AddException;
use App\Exception\RESTException;
use Hyperf\DbConnection\Db;
use mysql_xdevapi\Exception;
use Symfony\Component\Mime\Exception\AddressEncoderException;

class UserMoney extends BaseModel
{


    protected $table = 'user_money';
    protected $primaryKey = 'user_money_id';


    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'user_money_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'user_money_id' => 'integer',
        'user_id'       => 'integer',
        'total_money'   => 'float',
        'balance'       => 'float',
        'lock_balance'  => 'float',
        'points'        => 'integer',
        'lock_points'   => 'integer',
    ];

    /**
     * 减少可用余额,并增加锁定余额
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     */
    public function decBalanceAndIncLock($value = 0.0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查询可用余额是否充足
        if (bccomp($this->where($map)->value('balance', 0, true), $value, 2) === -1) {
            return $this->setError('账号可用余额不足');
        }

        if (!$this->where($map)->dec('balance', $value)->inc('lock_balance', $value)->update()) {
            return false;
        }

        return true;
    }

    /**
     * 增加可用余额,并减少锁定余额
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     */
    public function incBalanceAndDecLock($value = 0.0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查询锁定余额是否充足
        if (bccomp($this->where($map)->value('lock_balance', 0, true), $value, 2) === -1) {
            return $this->setError('账号锁定余额不足');
        }

        if (!$this->where($map)->dec('lock_balance', $value)->inc('balance', $value)->update()) {
            return false;
        }

        return true;
    }

    /**
     * 减少账号积分,并增加锁定积分
     * @access public
     * @param  int $value    数值
     * @param  int $clientId 账号编号
     * @return bool
     */
    public function decPointsAndIncLock($value = 0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查询账号积分是否充足
        if (bccomp($this->where($map)->value('points', 0, true), $value, 2) === -1) {
            return $this->setError('账号可用积分不足');
        }

        if (!$this->where($map)->dec('points', $value)->inc('lock_points', $value)->update()) {
            return false;
        }

        return true;
    }

    /**
     * 增加账号积分,并减少锁定积分
     * @access public
     * @param  int $value    数值
     * @param  int $clientId 账号编号
     * @return bool
     */
    public function incPointsAndDecLocl($value = 0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查询锁定积分是否充足
        if (bccomp($this->where($map)->value('lock_points', 0, true), $value, 2) === -1) {
            return $this->setError('账号锁定积分不足');
        }

        if (!$this->where($map)->inc('points', $value)->dec('lock_points', $value)->update()) {
            return false;
        }

        return true;
    }

    /**
     * 减少锁定余额
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     * @throws
     */
    public function decLockBalance($value = 0.0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查看锁定余额是否充足
        if (bccomp($this->where($map)->value('lock_balance', 0, true), $value, 2) === -1) {
            return $this->setError('账号锁定余额不足');
        }

        if ($this->where($map)->setDec('lock_balance', $value)) {
            return true;
        }

        return false;
    }

    /**
     * 减少锁定积分
     * @access public
     * @param  int $value    数值
     * @param  int $clientId 账号编号
     * @return bool
     * @throws
     */
    public function decLockPoints($value = 0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        // 查询锁定积分是否充足
        if (bccomp($this->where($map)->value('lock_points', 0, true), $value, 2) === -1) {
            return $this->setError('账号锁定积分不足');
        }

        if ($this->where($map)->setDec('lock_points', $value)) {
            return true;
        }

        return false;
    }

    /**
     * 增加或减少可用余额
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     * @throws
     */
    public function setBalance($value = 0.0, $clientId = 0)
    {
        if ($value == 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        if ($value > 0) {
            if ($this->where($map)->setInc('balance', $value)) {
                return true;
            }
        } else {
            if (bccomp($this->where($map)->value('balance', 0, true), $value, 2) === -1) {
                return $this->setError('账号可用余额不足');
            }

            if ($this->where($map)->setDec('balance', -$value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 增加或减少账号积分
     * @access public
     * @param  int $value    数值
     * @param  int $clientId 账号编号
     * @return bool
     * @throws
     */
    public function setPoints($value = 0, $clientId = 0)
    {
        if ($value == 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 查询条件
        $map['user_id'] = ['eq', $clientId];

        if ($value > 0) {
            if ($this->where($map)->setInc('points', $value)) {
                return true;
            }
        } else {
            if (bccomp($this->where($map)->value('points', 0, true), $value, 2) === -1) {
                return $this->setError('账号可用积分不足');
            }

            if ($this->where($map)->setDec('points', -$value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 增加账号累计消费金额,并调整账号会员等级
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     * @throws
     */
    public function incTotalMoney($value = 0.0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 获取当前账号信息
        $result = $this->where(['user_id' => ['eq', $clientId]])->find();
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        // 调整账号累计消费金额,并且重置账号会员等级
        if ($result->setInc('total_money', $value)) {
            $this->setUserLevel($result->getAttr('total_money'), $clientId);
        }

        return true;
    }

    /**
     * 减少账号累计消费金额,并调整账号会员等级
     * @access public
     * @param  float $value    数值
     * @param  int   $clientId 账号编号
     * @return bool
     * @throws
     */
    public function decTotalMoney($value = 0.0, $clientId = 0)
    {
        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 获取当前账号信息
        $result = $this->where(['user_id' => ['eq', $clientId]])->find();
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        // 调整账号累计消费金额,并且重置账号会员等级
        if ($result->setDec('total_money', $value)) {
            $this->setUserLevel($result->getAttr('total_money'), $clientId);
        }

        return true;
    }

    /**
     * 重置账号会员等级
     * @access public
     * @param  float $totalMoney 累计消费金额
     * @param  int   $clientId   账号编号
     * @return void
     */
    private function setUserLevel($totalMoney, $clientId)
    {
        $result = UserLevel::where(['amount' => ['elt', $totalMoney]])
            ->order(['amount' => 'desc'])
            ->find();

        if (!$result) {
            return;
        }

        $data['level_icon'] = $result->getAttr('icon');
        $data['user_level_id'] = $result->getAttr('user_level_id');

        User::where(['user_id' => ['eq', $clientId]])->save($data);
    }

    /**
     * 获取指定账号资金信息
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserMoneyInfo($data)
    {
        if (!$this->validateData($data, 'UserMoney')) {
            return false;
        }

        // 管理员可选择性查看,用户组必须指定
        $result = self::get(function ($query) use ($data) {
            $map['user_id'] = ['eq', is_client_admin() ? $data['client_id'] : get_client_id()];
            $query->field('total_money,balance,lock_balance,points,lock_points')->where($map);
        });

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }
}
