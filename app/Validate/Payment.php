<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付配置验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/26
 */

namespace App\Validate;


class Payment  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'payment_id'   => 'integer|gt:0',
        'name'         => 'require|max:50',
        'code'         => 'require|integer|between:0,9',
        'image'        => 'max:512',
        'is_deposit'   => 'require|in:0,1',
        'is_inpour'    => 'require|in:0,1',
        'is_payment'   => 'require|in:0,1',
        'is_refund'    => 'require|in:0,1',
        'setting'      => 'require|array',
        'model'        => 'max:50',
        'sort'         => 'integer|between:0,255',
        'status'       => 'in:0,1',
        'type'         => 'in:deposit,inpour,payment,refund',
        'is_select'    => 'in:0,1',
        'exclude_code' => 'arrayHasOnlyInts:zero',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'payment_id'   => '支付编号',
        'name'         => '支付名称',
        'code'         => '支付编码',
        'image'        => '支付图片',
        'is_deposit'   => '是否用于财务充值',
        'is_inpour'    => '是否用于账号充值',
        'is_payment'   => '是否用于订单支付',
        'is_refund'    => '是否支持原路退款',
        'setting'      => '支付配置',
        'model'        => '支付配置对应模型',
        'sort'         => '支付排序值',
        'status'       => '支付状态',
        'type'         => '支付类型',
        'is_select'    => '是否选择框',
        'exclude_code' => '排除支付编码',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'payment_id' => 'require|integer|gt:0',
            'image',
            'is_deposit',
            'is_inpour',
            'is_payment',
            'is_refund',
            'setting',
            'sort',
            'status',
        ],
        'del'    => [
            'payment_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'payment_id' => 'require|integer|gt:0',
        ],
        'info'   => [
            'code',
        ],
        'list'   => [
            'type',
            'is_select',
            'exclude_code',
        ],
        'sort'   => [
            'payment_id' => 'require|integer|gt:0',
            'sort'       => 'require|integer|between:0,255',
        ],
        'index'  => [
            'payment_id' => 'require|arrayHasOnlyInts',
        ],
        'status' => [
            'payment_id' => 'require|arrayHasOnlyInts',
            'status'     => 'require|in:0,1',
        ],
    ];
}
