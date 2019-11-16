<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    交易结算验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/20
 */

namespace App\Validate;


class Transaction  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'transaction_id' => 'integer|gt:0',
        'client_id'      => 'integer|gt:0',
        'action'         => 'max:80',
        'type'           => 'require|in:0,1',
        'amount'         => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'source_no'      => 'max:100',
        'remark'         => 'max:255',
        'cause'          => 'max:255',
        'module'         => 'require|in:points,money,card',
        'to_payment'     => 'require|integer|egt:0',
        'account'        => 'max:80',
        'begin_time'     => 'date|betweenTime|beforeTime:end_time',
        'end_time'       => 'date|betweenTime|afterTime:begin_time',
        'card_number'    => 'length:16',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:transaction_id,type,create_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'transaction_id' => '交易编号',
        'client_id'      => '账号编号',
        'action'         => '操作人',
        'type'           => '收支类型',
        'amount'         => '收支金额',
        'source_no'      => '交易来源订单号',
        'remark'         => '交易备注',
        'cause'          => '操作原因',
        'module'         => '收支模型',
        'to_payment'     => '交易来源',
        'account'        => '账号或昵称',
        'begin_time'     => '开始日期',
        'end_time'       => '结束日期',
        'card_number'    => '购物卡卡号',
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
        'list' => [
            'action',
            'type'       => 'in:0,1',
            'source_no',
            'module'     => 'in:points,money,card',
            'to_payment' => 'integer|egt:0',
            'account',
            'begin_time',
            'end_time',
            'card_number',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'item' => [
            'transaction_id' => 'require|integer|gt:0',
        ],
    ];
}
