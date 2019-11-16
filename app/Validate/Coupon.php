<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    优惠劵验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/18
 */

namespace App\Validate;


class Coupon  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'coupon_id'        => 'integer|gt:0',
        'name'             => 'require|max:50',
        'description'      => 'max:255',
        'guide'            => 'max:255',
        'type'             => 'require|in:0,1,2,3',
        'give_code'        => 'max:10',
        'money'            => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        'quota'            => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'category'         => 'arrayHasOnlyInts',
        'exclude_category' => 'arrayHasOnlyInts',
        'level'            => 'arrayHasOnlyInts',
        'frequency'        => 'integer|between:0,255',
        'give_num'         => 'require|integer|gt:0',
        'give_begin_time'  => 'require|date|betweenTime|beforeTime:give_end_time',
        'give_end_time'    => 'require|date|betweenTime|afterTime:give_begin_time',
        'use_begin_time'   => 'require|date|betweenTime|beforeTime:use_end_time',
        'use_end_time'     => 'require|date|betweenTime|afterTime:use_begin_time',
        'status'           => 'in:0,1',
        'is_invalid'       => 'in:0,1',
        'page_no'          => 'integer|gt:0',
        'page_size'        => 'integer|gt:0',
        'order_type'       => 'in:asc,desc',
        'order_field'      => 'in:coupon_id,name,type,give_num,receive_num,use_num,status,is_invalid',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'coupon_id'        => '优惠劵编号',
        'name'             => '优惠劵名称',
        'description'      => '优惠劵描述',
        'guide'            => '优惠劵引导地址',
        'type'             => '优惠劵类型',
        'give_code'        => '优惠劵领取码',
        'money'            => '优惠金额',
        'quota'            => '限制使用金额',
        'category'         => '限制商品类目',
        'exclude_category' => '排除商品类目',
        'level'            => '限制会员等级',
        'frequency'        => '限制领取次数',
        'give_num'         => '发放数量',
        'give_begin_time'  => '发放开始日期',
        'give_end_time'    => '发放结束日期',
        'use_begin_time'   => '使用开始日期',
        'use_end_time'     => '使用结束日期',
        'status'           => '优惠劵状态',
        'is_invalid'       => '优惠劵是否作废',
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
        'set'     => [
            'coupon_id' => 'require|integer|gt:0',
            'name',
            'description',
            'guide',
            'money',
            'quota',
            'category',
            'exclude_category',
            'level',
            'frequency',
            'give_num',
            'give_begin_time',
            'give_end_time',
            'use_begin_time',
            'use_end_time',
            'status',
            'is_invalid',
        ],
        'get'     => [
            'coupon_id' => 'require|integer|gt:0',
        ],
        'list'    => [
            'name' => 'max:50',
            'type' => 'in:0,1,2,3',
            'status',
            'is_invalid',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'del'     => [
            'coupon_id' => 'require|arrayHasOnlyInts',
        ],
        'status'  => [
            'coupon_id' => 'require|arrayHasOnlyInts',
            'status'    => 'require|in:0,1',
        ],
        'invalid' => [
            'coupon_id'  => 'require|arrayHasOnlyInts',
            'is_invalid' => 'require|in:0,1',
        ],
    ];
}
