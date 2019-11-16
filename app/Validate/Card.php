<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物卡验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/11/20
 */

namespace App\Validate;


class Card  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'card_id'          => 'integer|gt:0',
        'name'             => 'require|max:50',
        'description'      => 'max:255',
        'money'            => 'require|gt:0|regex:^\d+(\.\d{1,2})?$',
        'category'         => 'arrayHasOnlyInts',
        'exclude_category' => 'arrayHasOnlyInts',
        'give_num'         => 'require|integer|gt:0',
        'end_time'         => 'date',
        'status'           => 'in:0,1',
        'page_no'          => 'integer|gt:0',
        'page_size'        => 'integer|gt:0',
        'order_type'       => 'in:asc,desc',
        'order_field'      => 'in:card_id,name,money,give_num,active_num,create_time,end_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'card_id'          => '购物卡编号',
        'name'             => '购物卡名称',
        'description'      => '购物卡描述',
        'money'            => '购物卡面额',
        'category'         => '限制商品类目',
        'exclude_category' => '排除商品类目',
        'give_num'         => '发放数量',
        'end_time'         => '截止有效期',
        'status'           => '购物卡状态',
        'page_no'          => '页码',
        'page_size'        => '每页数量',
        'order_type'       => '排序方式',
        'order_field'      => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'card_id' => 'require|integer|gt:0',
            'name',
            'description',
            'category',
            'exclude_category',
            'status',
        ],
        'get'    => [
            'card_id' => 'require|integer|gt:0',
        ],
        'status' => [
            'card_id' => 'require|arrayHasOnlyInts',
            'status'  => 'require|in:0,1',
        ],
        'del'    => [
            'card_id' => 'require|arrayHasOnlyInts',
        ],
        'list'   => [
            'name' => 'max:50',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
