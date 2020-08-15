<?php
declare(strict_types=1);

namespace Mzh\Admin\Service;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Mzh\Admin\Entity\UserInfo;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Interfaces\UserInfoInterface;
use Mzh\Admin\Model\Admin\FrontRoutes;
use Mzh\Admin\Model\Admin\User;
use Mzh\Admin\Model\UserRole;
use Mzh\Helper\DbHelper\GetQueryHelper;
use Mzh\JwtAuth\Jwt;
use Mzh\JwtAuth\JwtBuilder;

class UserService
{
    use GetQueryHelper;

    /**
     * @Inject()
     * @var Jwt
     */
    protected $jwt;

    /**
     * @param $username
     * @param $password
     * @return array
     */
    public function manage_login($username, $password)
    {
        // 根据账号获取
        $result = User::query()->where('username', $username)->first();
        if (empty($result)) {
            throw new BusinessException(1000, '账号不存在');
        }
        if ($result->status !== 1) {
            throw new BusinessException(1000, '用户已被禁用');
        }
        if (!hash_equals($result->getAttribute('password'), User::passwordHash($password))) {
            throw new BusinessException(1000, '账号或密码错误');
        }
        $user_id = $result->id;
        $role = UserRole::query()->where('user_id', $user_id)->count();
        if ($role == 0) {
            throw new BusinessException(1000, '此账户未配置权无法登陆，请联系管理员！');
        }
        $result['login_time'] = get_current_date();
        $result['login_ip'] = getClientIp();
        $result->save();
        $userInfo = new UserInfo($result->toArray());
        Context::set(UserInfoInterface::class, $userInfo);

        $jwtBuilder = new JwtBuilder();
        $jwtBuilder->setIssuer('admin');
        $jwtBuilder->setAudience($user_id);
        //这里用简写，来减少加密后密文大小
        $jwtBuilder->setJwtData(['user_id' => $user_id]);
        $jwtBuilder->setExpiration(time() + 3600);
        $tokenObj = $this->jwt->createToken($jwtBuilder);
        session(UserInfoInterface::class, $userInfo, $jwtBuilder->getIssuer() . ':' . $userInfo->getUserId());
        return [
            'id' => $userInfo->getUserId(),
            'mobile' => $userInfo->getMobile(),
            'name' => $userInfo->getUsername(),
            'avatar' => $userInfo->getAvatar(),
            'token' => $tokenObj->getToken(),
        ];
    }

    public function menu($where)
    {
        $list = FrontRoutes::query()->when(isset($where['module']), function ($query) use ($where) {
            $query->where('module', $where['module']);
        })->where('status', 1)->orderBy('sort', 'desc')->get([
            'id',
            'pid',
            'label as menu_name',
            'is_menu as hidden',
            'is_scaffold as scaffold',
            'path as url',
            'open_type',
            'view',
            'icon',
        ])->each(function (&$item) {
            $item->hidden = !(bool)$item->hidden;
            $item->scaffold = (bool)$item->scaffold;
        });
        return ['menuList' => generate_tree($list->toArray())];
    }
}