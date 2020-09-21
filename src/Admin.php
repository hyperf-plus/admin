<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin;

use HPlus\Admin\Exception\ValidateException;
use HPlus\Admin\Model\Admin\Administrator;
use HPlus\Admin\Model\Admin\Menu;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthManager;

class Admin
{
    public static $metaTitle;

    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public static function setTitle($title)
    {
        self::$metaTitle = $title;
    }

    /**
     * Get admin title.
     *
     * @return string
     */
    public function title()
    {
        return self::$metaTitle ? self::$metaTitle : config('admin.title');
    }

    public function menu(Authenticatable $user)
    {
        if (! $user instanceof Authenticatable) {
            return [];
        }
        $menuClass = config('admin.database.menu_model');
        /** @var Menu $menuModel */
        $menuModel = $menuClass::query();
        $menuModel->where('is_menu', 1);
        $menuModel->with('roles:id,name,slug');
        /** @var Administrator $user */
        $permissionIds = $user->allPermissions()->pluck('id')->toArray();
        $userRolesIds = $user->roles()->pluck('id')->toArray();
        $list = $menuModel->get()->filter(function ($item) use ($user, $permissionIds, $userRolesIds) {
            $roles = $item->roles->pluck('id')->toArray();
            foreach ($userRolesIds as $role) {
                if (in_array($role, $roles)) {
                    return 1;
                }
            }
            $permissions = (array) $item->permission;
            foreach ($permissions as $permissionId) {
                if (in_array($permissionId, $permissionIds)) {
                    return 1;
                }
            }
            return 0;
        })->toArray();
        return generate_tree($list, 'parent_id');
    }

    public function user($token = null)
    {
        return $this->guard()->user($token);
    }

    /**
     * Attempt to get the guard from the local cache.
     */
    public function guard()
    {
        $guard = config('auth.default.guard') ?: 'jwt';
        return $this->authManager->guard($guard);
    }

    public function validatorData(array $all, $rules, $message = [])
    {
        $validator = Validator::make($all, $rules, $message);
        if ($validator->fails()) {
            throw new ValidateException(422, (string) $validator->errors()->first());
        }
        return $validator;
    }

    public function response($data, $message = '', $code = 200, $headers = [])
    {
        $re_data = [
            'code' => $code,
            'message' => $message,
        ];
        if ($data) {
            $re_data['data'] = $data;
        }
        $response = new Response();
        return $response->withBody(new SwooleStream(json_encode($re_data, 256)));
    }

    public function responseMessage($message = '', $code = 200)
    {
        return $this->response([], $message, $code);
    }

    public function responseError($message = '', $code = 400)
    {
        return $this->response([], $message, $code);
    }

    /**
     * @param $url
     * @param bool $isVueRoute
     * @param string $message
     * @param string $type info/success/warning/error
     */
    public function responseRedirect($url, $isVueRoute = true, $message = null, $type = 'success')
    {
        return $this->response([
            'url' => $url,
            'isVueRoute' => $isVueRoute,
            'type' => $type,
        ], $message, 301);
    }
}
