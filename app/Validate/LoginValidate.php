<?php

namespace App\Validate;


class LoginValidate extends Validate
{

    protected $rule = [
        'username' => 'require|min:5',
        'password' => 'require|min:5',
        'token' => 'require',
        'refresh_token' => 'require'
    ];

    protected $message = [
        'username.require' => '登录账号不能为空！',
        'username.min' => '登录账号长度不能少于5位有效字符！',
        'password.require' => '登录密码不能为空！',
        'password.min' => '登录密码长度不能少于5位有效字符！',
        'token.require' => 'token参数缺失！',
        'refresh_token.require'=>'refresh_token参数缺失！'
    ];

    protected $scene = [
        'login' => ['username', 'password'],
        'refreshToken' => ['refresh_token']
    ];

}