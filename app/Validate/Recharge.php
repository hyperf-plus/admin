<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    充值验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/27
 */

namespace App\Validate;


class Recharge  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'client_id'    => 'require|integer|gt:0',
        'money'        => ['float', 'regex' => '^(-|\+)?\d+(\.\d{1,2})?$'],
        'points'       => 'integer',
        'to_payment'   => 'require|integer|egt:0',
        'source_no'    => 'max:100',
        'cause'        => 'require|max:255',
        'payment_no'   => 'max:50',
        'type'         => 'in:return,notify',
        'request_type' => 'in:web,app',
        'order_no'     => 'max:50',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'client_id'    => '账号编号',
        'money'        => '金额',
        'points'       => '积分',
        'to_payment'   => '支付方式',
        'source_no'    => '来源订单号',
        'cause'        => '操作原因',
        'payment_no'   => '交易流水号',
        'type'         => '返回类型',
        'request_type' => '请求来源',
        'order_no'     => '订单号',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'finance' => [
            'client_id',
            'money',
            'points',
            'to_payment',
            'source_no',
            'cause',
        ],
        'return'  => [
            'to_payment',
        ],
        'user'    => [
            'money'        => 'require|float|egt:0.01|regex:^\d+(\.\d{1,2})?$',
            'to_payment',
            'payment_no',
            'request_type' => 'require|in:web,app',
        ],
        'order'   => [
            'to_payment',
            'order_no'     => 'require|max:50',
            'request_type' => 'require|in:web,app',
        ],
        'put'     => [
            'to_payment',
            'type' => 'require|in:return,notify',
        ],
    ];
}
