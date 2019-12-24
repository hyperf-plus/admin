<?php

declare(strict_types=1);
namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Model\MemberInfo;
use App\Service\UserService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Middleware(OauthMiddleware::class)
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class User extends AbstractController
{
    use GetFastAction;
    /**
     * @Inject()
     * @var UserService
     */
    private  $userService;

    /**
     * 获取用户列表
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->userService->list($data);
        return $this->json($result );
    }

    public function index()
    {
        $userInfo = MemberInfo::query()->find(getUserInfo()->getUid());
        return $this->json($userInfo);
    }

    /**
     * @return array
     */
    public function info()
    {
        $userInfo = MemberInfo::query()->find(getUserInfo()->getUid())??[];
        return $this->json($userInfo);
    }

    public function token()
    {

        return 12312;
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function update()
    {
        $data = $this->validated();
        $result = $this->userService->update($data);
        return $this->json($result);
    }


    public function edit()
    {
        $userInfo = MemberInfo::query(true)->findOrNew(getUserInfo()->getUid());
        $userInfo->save();
    }
}
