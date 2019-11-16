<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付日志验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/28
 */

namespace App\Validate;


class PaymentLog  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'payment_log_id' => 'integer|gt:0',
        'payment_no'     => 'max:50',
        'order_no'       => 'max:50',
        'out_trade_no'   => 'max:100',
        'amount'         => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'to_payment'     => 'integer|egt:0',
        'type'           => 'require|in:0,1',
        'status'         => 'in:0,1,2',
        'account'        => 'max:80',
        'begin_time'     => 'date|betweenTime|beforeTime:end_time',
        'end_time'       => 'date|betweenTime|afterTime:begin_time',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:payment_log_id,payment_time,create_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'payment_log_id' => '支付日志编号',
        'payment_no'     => '支付流水号',
        'order_no'       => '订单号',
        'out_trade_no'   => '交易号',
        'amount'         => '支付金额',
        'to_payment'     => '支付方式',
        'type'           => '支付类型',
        'status'         => '支付状态',
        'account'        => '账号或昵称',
        'begin_time'     => '开始日期',
        'end_time'       => '结束日期',
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
        'close' => [
            'payment_no' => 'require|max:50',
        ],
        'item'  => [
            'payment_no' => 'require|max:50',
            'type'       => 'in:0,1',
            'status',
        ],
        'list'  => [
            'payment_no',
            'order_no',
            'out_trade_no',
            'to_payment',
            'type' => 'in:0,1',
            'status',
            'account',
            'begin_time',
            'end_time',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
