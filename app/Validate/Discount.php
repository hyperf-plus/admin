<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品折扣验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/31
 */

namespace App\Validate;


class Discount  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'discount_id'    => 'integer|gt:0',
        'name'           => 'require|max:100',
        'type'           => 'require|in:0,1,2,3',
        'begin_time'     => 'require|date|betweenTime|beforeTime:end_time',
        'end_time'       => 'require|date|betweenTime|afterTime:begin_time',
        'status'         => 'in:0,1',
        'discount_goods' => 'require|array',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:promotion_id,name,all_goods,begin_time,end_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'discount_id'    => '折扣编号',
        'name'           => '折扣名称',
        'type'           => '折扣方式',
        'begin_time'     => '折扣开始日期',
        'end_time'       => '折扣结束日期',
        'status'         => '折扣状态',
        'discount_goods' => '折扣商品',
        'page_no'        => '页码',
        'page_size'      => '每页数量',
        'order_type'     => '排序方式',
        'order_field'    => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'discount_id' => 'require|integer|gt:0',
            'name',
            'type',
            'begin_time',
            'end_time',
            'status',
            'discount_goods',
        ],
        'item'   => [
            'discount_id' => 'require|integer|gt:0',
        ],
        'del'    => [
            'discount_id' => 'require|arrayHasOnlyInts',
        ],
        'status' => [
            'discount_id' => 'require|arrayHasOnlyInts',
            'status'      => 'require|in:0,1',
        ],
        'list'   => [
            'name'       => 'max:100',
            'status'     => 'in:0,1',
            'type'       => 'arrayHasOnlyInts',
            'begin_time' => 'date|betweenTime|beforeTime:end_time',
            'end_time'   => 'date|betweenTime|afterTime:begin_time',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
