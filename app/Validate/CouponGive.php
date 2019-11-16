<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    优惠劵发放验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/20
 */

namespace App\Validate;


class CouponGive  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'coupon_give_id' => 'integer|gt:0',
        'coupon_id'      => 'require|integer|gt:0',
        'username'       => 'arrayHasOnlyStrings',
        'user_level_id'  => 'arrayHasOnlyInts:zero',
        'give_number'    => 'integer|gt:0',
        'give_code'      => 'max:10',
        'exchange_code'  => 'max:10',
        'goods_id'       => 'arrayHasOnlyInts',
        'pay_amount'     => 'float|gt:0|regex:^\d+(\.\d{1,2})?$',
        'type'           => 'in:normal,used,invalid,delete',
        'account'        => 'max:80',
        'order_id'       => 'integer|gt:0',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'coupon_give_id' => '优惠劵发放编号',
        'coupon_id'      => '优惠劵编号',
        'username'       => '账号',
        'user_level_id'  => '等级编号',
        'give_number'    => '发放数量',
        'give_code'      => '优惠劵领取码',
        'exchange_code'  => '优惠劵兑换码',
        'goods_id'       => '商品编号',
        'pay_amount'     => '支付总金额',
        'type'           => '优惠劵状态选择',
        'account'        => '账号或昵称',
        'order_id'       => '订单编号',
        'page_no'        => '页码',
        'page_size'      => '每页数量',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'user'   => [
            'coupon_id',
            'username',
            'user_level_id',
        ],
        'live'   => [
            'coupon_id',
            'give_number' => 'require|integer|gt:0',
        ],
        'code'   => [
            'give_code' => 'require|max:10',
        ],
        'list'   => [
            'coupon_id' => 'integer|gt:0',
            'type',
            'account',
            'page_no',
            'page_size',
        ],
        'del'    => [
            'coupon_give_id' => 'require|arrayHasOnlyInts',
        ],
        'export' => [
            'coupon_id',
        ],
        'select' => [
            'goods_id'   => 'require|arrayHasOnlyInts',
            'pay_amount' => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        ],
        'check'  => [
            'coupon_give_id',
            'exchange_code',
            'goods_id'   => 'require|arrayHasOnlyInts',
            'pay_amount' => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        ],
        'use'    => [
            'coupon_give_id',
            'exchange_code',
            'order_id' => 'require|integer|gt:0',
        ],
    ];
}
