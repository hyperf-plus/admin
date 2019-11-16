<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单促销验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/31
 */

namespace App\Validate;


class Promotion  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'promotion_id'    => 'integer|gt:0',
        'name'            => 'require|max:100',
        'begin_time'      => 'require|date|betweenTime|beforeTime:end_time',
        'end_time'        => 'require|date|betweenTime|afterTime:begin_time',
        'status'          => 'in:0,1',
        'promotion_item'  => 'require|array',
        'page_no'         => 'integer|gt:0',
        'page_size'       => 'integer|gt:0',
        'order_type'      => 'in:asc,desc',
        'order_field'     => 'in:discount_id,name,default_dct,begin_time,end_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'promotion_id'    => '促销编号',
        'name'            => '促销名称',
        'begin_time'      => '促销开始日期',
        'end_time'        => '促销结束日期',
        'status'          => '促销状态',
        'promotion_item'  => '促销方式',
        'page_no'         => '页码',
        'page_size'       => '每页数量',
        'order_type'      => '排序方式',
        'order_field'     => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'promotion_id' => 'require|integer|gt:0',
            'name',
            'begin_time',
            'end_time',
            'status',
            'promotion_item',
        ],
        'item'   => [
            'promotion_id' => 'require|integer|gt:0',
        ],
        'status' => [
            'promotion_id' => 'require|arrayHasOnlyInts',
            'status'       => 'require|in:0,1',
        ],
        'del'    => [
            'promotion_id' => 'require|arrayHasOnlyInts',
        ],
        'list'   => [
            'name'       => 'max:100',
            'status',
            'begin_time' => 'date|betweenTime|beforeTime:end_time',
            'end_time'   => 'date|betweenTime|afterTime:begin_time',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
