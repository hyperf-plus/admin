<?php

namespace App\Validate;


class MenuValidate extends Validate
{

    protected $rule = [
        'id' => 'require',
        'pid' => 'require',
        'title' => 'require|min:2|max:10',
        'uri' => 'require',
        'params' => 'require',
//        'icon' => 'require'
    ];

    protected $message = [
        'id.require' => '参数id不能为空！',
        'pid.require' => '参数pid不能为空！',
        'title.require' => '菜单名称不能为空！',
        'title.min' => '菜单名称长度不能少于2位有效字符！',
        'title.max' => '菜单名称长度不能大于10位有效字符！',
        'uri' => '菜单地址不能为空！',
        'params' => '链接参数不能为空！',
    ];

    protected $scene = [
        'base' => ['id'],
        'add' => ['pid', 'title', 'uri', 'params'],
        'edit' => ['id', 'pid', 'title', 'uri', 'params'],
    ];

//    protected function checkAuthNodes($fieldValue)
//    {
//        var_dump($fieldValue);
//    }

    protected function checkParams($fieldValue)
    {

    }

}