<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Admin;
use App\Model\Token;
use App\Model\User;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\Di\Annotation\Inject;
use Mzh\JwtAuth\Jwt;
use Mzh\JwtAuth\JwtBuilder;
use Mzh\JwtAuth\JwtData;
use Mzh\Validate\Annotations\Validation;
use Mzh\Validate\Exception\ValidateException;
use Mzh\Validate\Validate\Validate;
use PDepend\Source\Parser\TokenException;

class UserService
{

    /**
     * @Inject()
     * @var Jwt
     */
    protected $jwt;

    /**
     * 验证某个字段
     * @access private
     * @param array $rules 验证规则
     * @param array $data 待验证数据
     * @return bool
     * @throws ValidateException
     */
    private function checkField($rules, $data)
    {
        $validate = new Validate($rules);
        return $validate->check($data);
    }

    /**
     * 验证账号是否合法
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws ValidateException
     */
    public function checkUserName($data)
    {
        $rule = 'require|alphaDash|length:4,20|unique:user,username';
        $rule .= sprintf(',%d,user_id', isset($data['exclude_id']) ? $data['exclude_id'] : 0);
        return $this->checkField(['username|账号' => $rule], $data);
    }

    /**
     * 验证账号手机是否合法
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws ValidateException
     */
    public function checkUserMobile($data)
    {
        $rule = 'require|number|length:7,15|unique:user,mobile';
        $rule .= sprintf(',%d,user_id', isset($data['exclude_id']) ? $data['exclude_id'] : 0);
        return $this->checkField(['mobile|手机号' => $rule], $data);
    }

    /**
     * 验证账号昵称是否合法
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws ValidateException
     */
    public function checkUserNick($data)
    {
        $rule = 'require|max:50|unique:user,nickname';
        $rule .= sprintf(',%d,user_id', isset($data['exclude_id']) ? $data['exclude_id'] : 0);
        return $this->checkField(['nickname|昵称' => $rule], $data);
    }


    /**
     * @param string $username 用户账户
     * @param string $password 用户密码
     * @param bool $isGetToken 是否获取token
     * @return array
     * @throws Exception
     */
    public function login(string $username, string $password, $isGetToken = false)
    {
        // 根据账号获取
        $result = Admin::query()->where(['username' => $username])->first();
        if (!$result) {
            throw new Exception('账号不存在');
        }
        if ($result->getAttribute('status') !== 1) {
            throw new Exception('账号已禁用');
        }
        if (!hash_equals($result->getAttribute('password'), userMd5($password))) {
            throw new Exception('账号或密码错误');
        }
        $data['last_login'] = time();
        $data['last_ip'] = getClientIp();
        $result->fillable(['last_login', 'last_ip'])->fill($data)->save();

        if (!$isGetToken) {
            return $result->toArray();
        }
        $adminId = $result->getAttribute('admin_id');
        $groupId = $result->getAttribute('group_id');
        return ['admin' => $result->toArray(), 'token' => $this->setToken($adminId, $groupId, 1, $username, $password)];

    }

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
        try {

            $jwtBuilder = new JwtBuilder();
            $jwtBuilder->setIssuer('api');
            $jwtBuilder->setAudience($id);
            //这里用简写，来减少加密后密文大小
            $jwtBuilder->setJwtData(['a' => $id, 't' => $type, 'g' => $group, 'u' => $username]);
            $tokenObj = $this->jwt->createToken($jwtBuilder);
            $refreshObj = $this->jwt->createToken($jwtBuilder, Jwt::SCOPE_REFRESH);
            // 准备数据
            $data = [
                'client_id' => $id,
                'group_id' => $group,
                'username' => $username,
                'corp_id' => getTenant()->getId(),
                'client_type' => $type,
                'platform' => $platform,
                'token' => $tokenObj->getToken(),
                'token_expires' => $tokenObj->getExpiration(),
                'refresh' => $refreshObj->getRefreshToken(),
                'refresh_expires' => $refreshObj->getExpiration(),
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
     * @return false|array
     * @throws
     */
    public function refreshUser($type, $refresh)
    {
        try {
            // 验证refresh是否正确，正确返回jwt对象
            $jwtBuilder = $this->jwt->verifyRefreshToken($refresh);

            $tokenObj = $this->jwt->createToken($jwtBuilder);
            $refreshObj = $this->jwt->createToken($jwtBuilder, Jwt::SCOPE_REFRESH);
            // 准备数据
            $data = [
                'client_id' => $tokenObj->getJwtData()['a'],
                'group_id' => $tokenObj->getJwtData()['g'],
                'username' => $tokenObj->getJwtData()['u'],
                'client_type' => $tokenObj->getJwtData()['t'],
                'token' => $tokenObj->getToken(),
                'token_expires' => $tokenObj->getExpiration(),
                'refresh' => $refreshObj->getToken(),
                'refresh_expires' => $refreshObj->getExpiration(),
            ];
            return $data;
        } catch (\Exception $e) {
            throw new TokenException($e->getMessage());
        }
    }

    /**
     * @Validation(mode="User",field="data",scene="list",filter=true)
     * @param array $data
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function list(array $data, $page = 0, $pageSize = 25)
    {
        // 排序方式
        $data['order_type'] ??= 'desc';
        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;
        // 搜索条件
        $db = User::query()->where(function ($query) use ($data) {
            /*** @var \Hyperf\Database\Model\Builder $query */
            if (isset($data['account']) && $data['account'] != '') {
                $query->where(function ($db) use ($data) {
                    /*** @var \Hyperf\Database\Model\Builder $db */
                    $db->where('user.username', 'like', '%' . $data['account'] . '%', 'or');
                    $db->where('user.mobile', 'like', '%' . $data['account'] . '%', 'or');
                    $db->where('user.nickname', 'like', '%' . $data['account'] . '%', 'or');
                });
            }
            if (isset($data['user_level_id']) && $data['user_level_id'] != '') {
                $query->where('user.user_level_id', $data['user_level_id']);
            }
            if (isset($data['status']) && $data['status'] != '') {
                $query->where('user.status', $data['status']);
            }
            if (isset($data['group_id']) && $data['group_id'] != '') {
                $query->where('user.group_id', $data['group_id']);
            }
        });
        // 排序的字段
        $orderField = 'user.user_id';
        if (isset($data['order_field'])) {
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
        $result = $db->orderBy($orderField, $data['order_type'])->with('getAuthGroup')->with('getUserLevel')->paginate($data['page_size'], ['*'], '', $data['page_no']);
        if ($result) {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
        return ['items' => [], 'total_result' => 0];
    }

    /**
     * @Validation(mode="User",value="set",filter=true)
     * @param array $data
     * @return array|bool
     * @throws Exception
     */
    public function update(array $data)
    {
        $result = User::query()->find($data['user_id']);
        //只允许更新以下字段
        if ($result->fillable(['group_id', 'nickname', 'head_pic', 'birthday'])->fill($data)->save()) {
            return $result->toArray();
        }
        throw new Exception('更新失败！');
    }

}
