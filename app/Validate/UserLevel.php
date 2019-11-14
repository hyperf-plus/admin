<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号等级验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/30
 */

namespace App\Validate;


class UserLevel  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'user_level_id' => 'integer|gt:0',
        'name'          => 'require|length:1,30',
        'icon'          => 'max:512',
        'amount'        => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'discount'      => 'require|integer|between:0,100',
        'description'   => 'max:200',
        'order_type'    => 'in:asc,desc',
        'order_field'   => 'in:user_level_id,amount,discount',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'user_level_id' => '等级编号',
        'name'          => '等级名称',
        'icon'          => '等级图标',
        'amount'        => '消费金额',
        'discount'      => '折扣',
        'description'   => '等级描述',
        'order_type'    => '排序方式',
        'order_field'   => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'  => [
            'user_level_id' => 'require|integer|gt:0',
            'name',
            'icon',
            'amount',
            'discount',
            'description',
        ],
        'del'  => [
            'user_level_id' => 'require|arrayHasOnlyInts',
        ],
        'item' => [
            'user_level_id' => 'require|integer|gt:0',
        ],
        'list' => [
            'order_type',
            'order_field',
        ],
    ];
}
