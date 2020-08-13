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
use Mzh\Admin\Model\UserRole;
use Mzh\Admin\Service\UserService;
use Mzh\Admin\Traits\GetApiDelete;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Validate\UserValidation;
use Mzh\Helper\DbHelper\QueryHelper;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\Body;
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
    protected $serviceClass = UserService::class;

    use GetApiUI;
    use GetApiList;
    use GetApiDelete;


    public function _list_before(QueryHelper &$helper)
    {
        $helper->like('username,realname');
        $helper->dateBetween('created_at#create_at');
    }


    /**
     * 用户删除成功后清理相关数据
     * @param $data
     */
    public function _delete_after($id)
    {
        #1、删除用户组，
        UserRole::query()->where('user_id', $id)->delete();
    }


    public function _form_before(&$data)
    {
        if ($this->isGet()) {
            return;
        }
        if (!empty($data['pwd'])) {
            $data['password'] = \Mzh\Admin\Model\Admin\User::passwordHash($data['pwd']);
        }
    }

    public function _form_after(&$data)
    {
        if ($this->isGet()) {
            return;
        }

        $role_ids = (array)$this->request->post('role_ids', []);
        $role_ids = array_filter(array_unique($role_ids));
        #删除旧权限，重新插入新的权限组

        if (!$data->wasRecentlyCreated) {
            UserRole::query()->where('user_id', $data->id)->delete();
        }
        $user_roles = [];
        foreach ($role_ids as $role_id) {
            $user_roles[] = [
                'user_id' => $data->id,
                'module' => \Mzh\Admin\Model\AuthRule::query()->where('id', $data->id)->first(['module'])['module'] ?? 'admin',
                'role_id' => (int)$role_id,
            ];
        }
        if (!empty($user_roles)) {
            UserRole::query()->insert($user_roles);
        }
    }

    /**
     * @PostApi(security=false)
     * @Body(validate=UserValidation::class,scene="login")
     * @return ResponseInterface
     */
    public function login()
    {
        $data = $this->request->getParsedBody();
        $result = $this->getService()->manage_login($data['username'], $data['password']);
        return $this->response->json($result);
    }

    /**
     * @GetApi(security=true)
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
