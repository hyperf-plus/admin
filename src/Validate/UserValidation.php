<?php
declare(strict_types=1);

namespace Mzh\Admin\Validate;

use Mzh\Validate\Validate\Validate;

class UserValidation extends Validate
{
    protected $rule = [
        'username' => 'require',
        'realname' => 'max:255',
        'password' => 'max:255',
        'mobile' => 'max:255',
        'email' => 'max:255',
        'status' => 'max:255',
        'login_time' => 'max:255',
        'login_ip' => 'max:255',
        'is_admin' => 'max:255',
        'is_default_pass' => 'max:255',
        'qq' => 'max:255',
        'roles' => 'max:255',
        'sign' => 'max:255',
        'avatar' => 'max:255',
        'avatar_small' => 'max:255',
        'page' => 'integer',
        'limit' => 'integer|gt:0',
    ];

    protected $field = [
        'username' => '用户名',
        'realname' => '',
        'password' => '',
        'mobile' => '',
        'email' => '',
        'status' => '',
        'login_time' => '',
        'login_ip' => '',
        'is_admin' => 'is admin',
        'is_default_pass' => '是否初始密码1:是,0:否',
        'qq' => '用户qq',
        'roles' => '',
        'sign' => '签名',
        'avatar' => '',
        'avatar_small' => '',
        'page' => '页码',
        'limit' => '每页条数',
    ];

    protected $scene = [
        'detail' => ['username'],
        'login' => ['username', 'password'],
        'update' => [
            'realname',
            'password',
            'mobile',
            'email',
            'status',
            'login_time',
            'login_ip',
            'is_admin',
            'is_default_pass',
            'qq',
            'roles',
            'sign',
            'avatar',
            'avatar_small',
        ],
        'delete' => ['username'],
        'list' => ['limit', 'page'],
        'menu' => ['limit', 'page', 'tab_id'],
        'sort' => ['username', 'sort'],
        'status' => ['username', 'status' => 'require|in:0,1'],
        'create' => [
            'username',
            'realname',
            'password',
            'mobile',
            'email',
            'status',
            'login_time',
            'login_ip',
            'is_admin',
            'is_default_pass',
            'qq',
            'roles',
            'sign',
            'avatar',
            'avatar_small',
        ],
    ];
}
