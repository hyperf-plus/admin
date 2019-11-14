<?php

namespace App\Model;


use Hyperf\Database\Model\Model;
use PDepend\Source\Parser\TokenException;

class Token extends BaseModel
{
    public $timestamps = false;
    protected $table = 'token';
    protected $primaryKey = 'token_id';
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'token_id',
        'client_id',
        'username',
        'client_type',
        'platform',
    ];

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'token_id',
        'client_id',
        'client_type',
        'code',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'token_id' => 'integer',
        'client_id' => 'integer',
        'group_id' => 'integer',
        'client_type' => 'integer',
        'token_expires' => 'integer',
        'refresh_expires' => 'integer',
    ];

    /**
     * 产生Token
     * @access public
     * @param int $id 编号
     * @param int $group 用户组编号
     * @param int $type 顾客或管理组
     * @param string $username 账号
     * @param string $platform 来源平台
     * @return false|array
     * @throws
     */
    public function setToken($id, $group, $type, $username, $platform)
    {
        $code = uuid(16);
        $token = user_md5(sprintf('%d%d%s', $id, $type, $code));
        $expires = time() + (30 * 24 * 60 * 60); // 30天

        // 准备数据
        $data = [
            'client_id' => $id,
            'group_id' => $group,
            'username' => $username,
            'client_type' => $type,
            'platform' => $platform,
            'code' => $code,
            'token' => $token,
            'token_expires' => $expires,
            'refresh' => user_md5(uuid(32) . $token),
            'refresh_expires' => $expires + (1 * 24 * 60 * 60),
        ];
        $map = [];
        // 搜索条件
        $map['client_id'] = $id;
        $map['client_type'] = $type;
        $map['platform'] = $platform;
        /**
         * @var Model $result
         */
        $result = $this->where($map)->first();
        if (false === $result) {
            throw new TokenException('Token不存在');
        }
        if ($result && false !== $this->where($map)->update($data)) {
            return $result->setHidden(['username', 'platform'])->toArray();
        }
        if (false !== $this->forceFill($data)->save($map)) {
            return $this->setHidden(['username', 'platform'])->toArray();
        }
        throw new TokenException('Token生成错误');
    }

    /**
     * 刷新Token
     * @access public
     * @param int $type 顾客或管理组
     * @param string $refresh 刷新令牌
     * @param string $oldToken 原授权令牌
     * @return false|array
     * @throws
     */
    public function refreshUser($type, $refresh, $oldToken)
    {
        // 搜索条件
        $map['client_id'] = ['eq', get_client_id()];
        $map['client_type'] = ['eq', $type];
        $map['token'] = ['eq', $oldToken];

        $result = $this->where($map)->find();
        if (!$result) {
            return is_null($result) ? $this->setError('refresh不存在') : false;
        }

        if (time() > $result->getAttr('refresh_expires')) {
            return $this->setError('refresh已过期');
        }

        if (!hash_equals($result->getAttr('refresh'), $refresh)) {
            return $this->setError('refresh错误');
        }

        // 准备更新数据
        $code = uuid(8);
        $token = user_md5(sprintf('%d%d%s', get_client_id(), $type, $code));
        $expires = time() + (30 * 24 * 60 * 60); // 30天

        $data = [
            'code' => $code,
            'token' => $token,
            'token_expires' => $expires,
            'refresh' => user_md5(rand_string() . $token),
            'refresh_expires' => $expires + (1 * 24 * 60 * 60),
        ];

        if (false !== $result->save($data)) {
            return $result->hidden(['username', 'platform'])->toArray();
        }

        return false;
    }
}
