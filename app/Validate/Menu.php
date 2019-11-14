<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    菜单管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/9
 */

namespace App\Validate;


class Menu  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'menu_id'   => 'integer|gt:0',
        'parent_id' => 'integer|egt:0',
        'name'      => 'require|max:32',
        'alias'     => 'max:16',
        'icon'      => 'max:64',
        'remark'    => 'max:255',
        'module'    => 'require|checkModule',
        'type'      => 'require|in:0,1',
        'url'       => 'max:255',
        'params'    => 'max:255',
        'target'    => 'in:_self,_blank',
        'is_navi'   => 'in:0,1',
        'sort'      => 'integer|between:0,255',
        'status'    => 'in:0,1',
        'level'     => 'integer|egt:0',
        'is_layer'  => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'menu_id'   => '菜单编号',
        'parent_id' => '菜单上级编号',
        'name'      => '菜单名称',
        'alias'     => '菜单别名',
        'icon'      => '菜单图标',
        'remark'    => '菜单备注',
        'module'    => '所属模块',
        'type'      => '链接类型',
        'url'       => '链接地址',
        'params'    => '链接参数',
        'target'    => '打开方式',
        'is_navi'   => '是否属于导航菜单',
        'sort'      => '菜单排序值',
        'status'    => '菜单状态',
        'level'     => '菜单深度',
        'is_layer'  => '是否返回本级菜单',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'item'   => [
            'menu_id' => 'require|integer|gt:0',
        ],
        'set'    => [
            'menu_id' => 'require|integer|gt:0',
            'parent_id',
            'name',
            'alias',
            'icon',
            'remark',
            'type',
            'url',
            'params',
            'target',
            'is_navi',
            'sort',
        ],
        'del'    => [
            'menu_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'menu_id' => 'integer|egt:0',
            'module',
            'level',
            'is_layer',
            'is_navi',
            'status',
        ],
        'sort'   => [
            'menu_id' => 'require|integer|gt:0',
            'sort'    => 'require|integer|between:0,255',
        ],
        'index'  => [
            'menu_id' => 'require|arrayHasOnlyInts',
        ],
        'status' => [
            'menu_id' => 'require|integer|gt:0',
            'status'  => 'require|in:0,1',
        ],
        'navi'   => [
            'menu_id' => 'integer|egt:0',
            'is_layer',
        ],
        'url'    => [
            'url' => 'max:100',
            'is_layer',
        ],
        'nac'    => [
            'menu_id' => 'require|arrayHasOnlyInts',
            'is_navi' => 'require|in:0,1',
        ],
        'auth'   => [
            'menu_id' => 'integer|egt:0',
            'module',
        ],
    ];
}
