<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单退款验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/9/25
 */

namespace App\Validate;


class OrderRefund  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'order_refund_id' => 'integer|gt:0',
        'refund_no'       => 'max:50',
        'order_no'        => 'require|max:50',
        'out_trade_no'    => 'max:100',
        'out_trade_msg'   => 'max:200',
        'amount'          => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'to_payment'      => 'require|integer|egt:0',
        'status'          => 'in:0,1,2,3',
        'payment_no'      => 'max:50',
        'begin_time'      => 'date|betweenTime|beforeTime:end_time',
        'end_time'        => 'date|betweenTime|afterTime:begin_time',
        'account'         => 'max:80',
        'page_no'         => 'integer|gt:0',
        'page_size'       => 'integer|gt:0',
        'order_type'      => 'in:asc,desc',
        'order_field'     => 'in:order_refund_id,create_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'order_refund_id' => '退款日志编号',
        'refund_no'       => '退款流水号',
        'order_no'        => '订单号',
        'out_trade_no'    => '退款交易号',
        'out_trade_msg'   => '交易信息',
        'amount'          => '退款金额',
        'to_payment'      => '退款方式',
        'status'          => '退款状态',
        'payment_no'      => '支付流水号',
        'begin_time'      => '开始日期',
        'end_time'        => '结束日期',
        'account'         => '账号或昵称',
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
        'refund' => [
            'order_no',
        ],
        'retry'  => [
            'refund_no' => 'require|max:50',
        ],
        'query'  => [
            'refund_no' => 'require|max:50',
        ],
        'list'   => [
            'refund_no',
            'order_no'   => 'max:50',
            'out_trade_no',
            'payment_no',
            'to_payment' => 'integer|egt:0',
            'status',
            'begin_time',
            'end_time',
            'account',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
