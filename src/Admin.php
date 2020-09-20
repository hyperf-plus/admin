<?php
declare(strict_types=1);

namespace HPlus\Admin;

use HPlus\Admin\Exception\ValidateException;
use HPlus\Admin\Model\Admin\Administrator;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;
use HPlus\Admin\Model\Admin\Menu;
use Hyperf\Utils\Arr;

class Admin
{

    public static $metaTitle;

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

    public function menu($user_id = 0)
    {
        $menuClass = config('admin.database.menu_model');
        /** @var Menu $menuModel */
        $menuModel = $menuClass::query();
        $menuModel->where('is_menu', 1);
        $menuModel->with('roles:id,name,slug');
        $user = ($user_id == 0) ? auth()->user() : Administrator::findFromCache($user_id);
        $list = $menuModel->get()->filter(function ($item) use ($user) {
            return 1;// $checkRoles || $checkPermission;
        })->toArray();
        return generate_tree($list, 'parent_id');
    }

    /**
     */
    public function user()
    {
        return $this->guard()->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     */
    public function guard()
    {
        $guard = config('admin.auth.guard') ?: 'admin';

        return Auth()->guard($guard);
    }

    public function validatorData(array $all, $rules, $message = [])
    {
        $validator = Validator::make($all, $rules, $message);
        if ($validator->fails()) {
            throw new ValidateException(422, (string)$validator->errors()->first());
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
            'type' => $type
        ], $message, 301);
    }
}
