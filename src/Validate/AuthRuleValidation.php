<?php
declare(strict_types=1);

namespace Mzh\Admin\Validate;

use Mzh\Validate\Validate\Validate;

class AuthRuleValidation extends Validate
{
    protected $rule = [
        'id' => 'integer|gt:0',
        'module' => 'alpha',
        'group_id' => 'integer',
        'name' => 'require',
        'permissions' => 'array',
        'menu_auth' => 'array',
        'log_auth' => 'array',
        'sort' => 'integer',
        'status' => 'integer',
        'is_delete' => 'integer',
        'updated_at' => 'max:255',
        'created_at' => 'max:255',
        'pid' => 'max:255',
        'page' => 'integer',
        'limit' => 'integer|gt:0',
    ];

    protected $field = [
        'id' => 'ID',
        'module' => '所属模块',
        'group_id' => '用户组Id',
        'name' => '规则名称',
        'menu_auth' => '菜单权限',
        'log_auth' => '记录权限',
        'sort' => '排序',
        'status' => '0=禁用 1=启用',
        'is_delete' => '0=为删除 1=已删除',
        'updated_at' => '更新时间',
        'created_at' => '创建时间',
        'pid' => '',
        'page' => '页码',
        'limit' => '每页条数',
    ];

    protected $scene = [
        'detail' => ['module'],
        'update' => [
            'id',
            'group_id',
            'name',
            'menu_auth',
            'permissions',
            'log_auth',
            'sort',
            'status',
            'is_delete',
            'updated_at',
            'created_at',
            'pid',
        ],
        'delete' => ['id' => 'require|integer'],
        'list' => ['limit', 'page'],
        'sort' => ['id' => 'require|integer', 'sort' => 'require'],
        'status' => ['id', 'status' => 'require|in:0,1'],
        'create' => [
            'group_id',
            'name',
            'menu_auth',
            'log_auth',
            'sort',
            'status',
            'is_delete',
            'updated_at',
            'created_at',
            'pid',
        ],
    ];
}
