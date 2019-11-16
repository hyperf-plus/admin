<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    客服验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/28
 */

namespace App\Validate;


class Support  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'support_id'  => 'integer|gt:0',
        'type_name'   => 'require|max:50',
        'nick_name'   => 'require|max:50',
        'code'        => 'require|max:150',
        'sort'        => 'integer|between:0,255',
        'status'      => 'in:0,1',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:support_id,type_name,nick_name,sort,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'support_id'  => '客服编号',
        'type_name'   => '客服组名称',
        'nick_name'   => '客服昵称',
        'code'        => '联系方式',
        'sort'        => '客服排序值',
        'status'      => '客服状态',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'support_id' => 'require|integer|gt:0',
            'type_name',
            'nick_name',
            'code',
            'sort',
            'status',
        ],
        'del'    => [
            'support_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'support_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'type_name' => 'max:50',
            'nick_name' => 'max:50',
            'status',
            'order_type',
            'order_field',
        ],
        'status' => [
            'support_id' => 'require|arrayHasOnlyInts',
            'status'     => 'require|in:0,1',
        ],
        'sort'   => [
            'support_id' => 'require|integer|gt:0',
            'sort'       => 'require|integer|between:0,255',
        ],
        'index'  => [
            'support_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
