<?php
/**
 *菜单管理验证器
 */

namespace Mzh\Admin\Validate;


use Mzh\Validate\Validate\Validate;

class DefaultValidation extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'id' => 'integer|gt:0',
        'name' => 'require|max:32',
        'status' => 'in:0,1',
        'sort' => 'integer',
        'is_delete' => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'id' => '编号',
        'name' => '名称',
        'sort' => '排序值',
        'status' => '状态',
        'is_delete' => '是否删除',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'item' => [
            'menu_id' => 'require|integer|gt:0',
        ],
        'update' => [
            'id' => 'require|integer|gt:0',
            'name',
            'sort',
        ],
        'delete' => [
            'id' => 'require|integer|gt:0',
        ],
        'batch_del' => [
            'ids' => 'require|intOrArrayInt',
        ],
        'list' => [
            'id' => 'integer|egt:0',
            'name',
            'status',
        ],
        'sort' => [
            'menu_id' => 'require|integer|gt:0',
            'sort' => 'require|integer|between:0,255',
        ],
        'state' => [
            'menu_id' => 'require|intOrArrayInt',
            'status' => 'require|in:0,1',
        ],
        'auth' => [
            'menu_id' => 'integer|egt:0'
        ],
        'create' => [
            'name' => 'require|max:32',
            'status' => 'in:0,1',
            'sort' => 'integer',
            'is_delete' => 'in:0,1'
        ]
    ];
}
