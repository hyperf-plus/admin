<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\AdminService;
use App\Service\UserService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Mzh\Validate\Annotations\RequestValidation;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class Admin extends AbstractController
{
    use GetFastAction;

    /**
     * @Inject()
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @Inject()
     * @var AdminService
     */
    protected AdminService $adminService;

    /**
     * 登录方法
     * @RequestValidation(mode="Admin",filter=true,throw=true)
     * @return array
     * @throws \Exception
     */
    public function login()
    {
        $data = $this->validated();
        $result = $this->userService->login($data['username'], $data['password'], TRUE);
        return $this->json($result);
    }

    /**
     * 登录方法
     * @RequestValidation(mode="Admin",scene="refresh",filter=true)
     * @return array
     * @throws \Exception
     */
    public function refresh_token()
    {
        $data = $this->validated();
        $result = $this->userService->refreshUser(getUserInfo()->getType(), $data['refresh']);
        return $this->json($result);
    }


    /**
     * @param $data
     * @param $page
     * @param $pageSize
     * @return array
     */
    /**
     * @return array
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $data['page'] ??= 1;
        $data['page_size'] ??= 25;
        $result = $this->adminService->list($data, $data['page'], $data['page_size']);
        return $this->json($result);
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function update()
    {
        $data = $this->validated();
        $result = $this->adminService->update($data);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function create()
    {
        $data = $this->validated();
        $result = $this->adminService->create($data);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function reset()
    {
        $data = $this->validated();
        $result = $this->adminService->reset($data);
        return $this->success($result, "重置成功，");
    }


}
