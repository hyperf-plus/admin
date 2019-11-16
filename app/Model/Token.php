<?php

namespace App\Model;


use App\Constants\Constants;
use App\Util\Jwt\Jwt;
use App\Util\Jwt\JwtData;
use Hyperf\Di\Annotation\Inject;
use PDepend\Source\Parser\TokenException;

class Token extends BaseModel
{
    public $timestamps = false;
    protected $table = 'token';
    protected $primaryKey = 'token_id';


    /**
     * @Inject()
     * @var Jwt $jwt
     */
    protected $jwt;

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
        $curr_time = time();
        $expires = $curr_time + 7200;
        $refresh_expires = $expires + (1 * 24 * 60 * 60);
        try {
            $payload = new JwtData();
            $payload->setScope(Constants::SCOPE_ROLE);
            $payload->setIssuer($username);
            $payload->setJwtData([
                'id' => $id,
                'group' => $group
            ]);
            $payload->setExpiration($expires);
            $payload->setSubject(env('JWT_NAME'));
            $payload->setIssuedAt($curr_time);
            $payload->offsetSet("jti", $id);   //该JWT的签发者
            $token = $this->jwt->getToken($payload->toArray());
            $payload->setScope(Constants::SCOPE_REFRESH);
            $payload->setExpiration($refresh_expires);
            $refresh = $this->jwt->getToken($payload->toArray());
            // 准备数据
            $data = [
                'client_id' => $id,
                'group_id' => $group,
                'username' => $username,
                'client_type' => $type,
                'platform' => $platform,
                'token' => $token,
                'token_expires' => $expires,
                'refresh' => $refresh,
                'refresh_expires' => $refresh_expires,
            ];
            return $data;
        } catch (\Exception $e) {
            throw new TokenException($e->getMessage());
        }

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
