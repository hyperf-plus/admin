<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Mzh\Admin\Controller\Api;

use Hyperf\Di\Annotation\Inject;
use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Service\UserService;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Validate\UserValidation;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\PostApi;
use Mzh\Swagger\Annotation\Query;
use Psr\Http\Message\ResponseInterface;

/**
 * @ApiController(tag="用户模块", description="资讯列表/分类/详情")
 * Class IndexController
 * @package Mzh\Admin\Controller
 */
class User extends AbstractController
{

    /**
     * @Inject()
     * @var UserService $userService
     */
    protected $userService;

    protected $modelClass = \Mzh\Admin\Model\Admin\User::class;

    use GetApiUI;
    use GetApiList;

    /**
     * @PostApi()
     * @return ResponseInterface
     */
    public function login()
    {
        return $this->response->json([
            'id' => 1,
            'mobile' => '13422222222',
            'name' => 'admin',
            'avatar' => '',
            'token' => 'token',
        ]);
    }

    /**
     * @GetApi()
     * @Query(validate=UserValidation::class,scene="menu")
     * @return ResponseInterface
     */
    public function menu()
    {
        $data = $this->request->all();
        $result = $this->userService->menu($data);
        return $this->response->json($result);
    }

    /**
     * @GetApi()
     * @return ResponseInterface
     */
    public function exports()
    {
        return $this->response->json([]);
    }
}
