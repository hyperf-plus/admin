<?php
declare(strict_types=1);
namespace App\Validate;

use Hyperf\DbConnection\Db;
use Mzh\Validate\Validate\Validate;

class AdminValidation extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected  $rule = [
        'client_id' => 'integer|gt:0',
        'username' => 'require|alphaDash|length:4,20|unique:admin,username,0,admin_id',
        'password' => 'require|min:6|confirm',
        'group_id' => 'require|integer|gt:0',
        'nickname' => 'require|max:50|unique:admin,nickname,0,admin_id',
        'head_pic' => 'array',
        'status' => 'in:0,1',
        'password_old' => 'min:6',
        'account' => 'max:80',
        'platform' => 'max:50',
        'refresh' => 'length:32',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|gt:0',
        'order_type' => 'in:asc,desc',
        'order_field' => 'in:admin_id,username,group_id,nickname,last_login,status,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'client_id' => '账号编号',
        'username' => '账号',
        'password' => '密码',
        'group_id' => '所属用户组',
        'nickname' => '昵称',
        'head_pic' => '头像',
        'status' => '账号状态',
        'password_old' => '原始密码',
        'account' => '账号、昵称',
        'platform' => '来源平台',
        'page_no' => '页码',
        'page_size' => '每页数量',
        'order_type' => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set' => [
            'client_id' => 'require|integer|gt:0',
            'nickname' => 'max:50|unique:admin,username,0,admin_id',
            'group_id' => 'integer|gt:0',
            'head_pic' => '',
        ],
        'status' => [
            'admin_id' => 'require|arrayHasOnlyInts',
            'status' => 'require|in:0,1',
        ],
        'change' => [
            'client_id' => 'require|integer|gt:0',
            'password',
            'password_old' => 'require|min:6',
        ],
        'reset' => [
            'admin_id' => 'require|integer|gt:0',
        ],
        'del' => [
            'client_id' => 'require|arrayHasOnlyInts',
        ],
        'item' => [
            'client_id' => 'require|integer|gt:0',
        ],
        'list' => [
            'account',
            'group_id' => 'integer|gt:0',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'login' => [
            'username' => 'require|alphaDash|length:4,20',
            'password' => 'require|min:6',
            'platform' => 'max:50'
        ],
        'refresh' => [
            'refresh' => 'require',
        ],
    ];
}
