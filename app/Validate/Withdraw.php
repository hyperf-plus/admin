<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/21
 */

namespace App\Validate;


class Withdraw  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'withdraw_id'      => 'integer|gt:0',
        'withdraw_no'      => 'max:50',
        'withdraw_user_id' => 'require|integer|gt:0',
        'money'            => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        'remark'           => 'max:255',
        'status'           => 'in:0,1,2,3,4',
        'account'          => 'max:80',
        'begin_time'       => 'date|betweenTime|beforeTime:end_time',
        'end_time'         => 'date|betweenTime|afterTime:begin_time',
        'page_no'          => 'integer|gt:0',
        'page_size'        => 'integer|gt:0',
        'order_type'       => 'in:asc,desc',
        'order_field'      => 'in:withdraw_id,withdraw_no,create_time,update_time,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'withdraw_id'      => '提现编号',
        'withdraw_no'      => '提现单号',
        'withdraw_user_id' => '提现账号编号',
        'money'            => '提现金额',
        'remark'           => '提现备注',
        'status'           => '提现状态',
        'account'          => '账号或昵称',
        'begin_time'       => '开始日期',
        'end_time'         => '结束日期',
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
        'item'     => [
            'withdraw_no' => 'require|max:50',
        ],
        'list'     => [
            'withdraw_no',
            'status',
            'account',
            'begin_time',
            'end_time',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'cancel'   => [
            'withdraw_no' => 'require|max:50',
        ],
        'process'  => [
            'withdraw_no' => 'require|max:50',
        ],
        'complete' => [
            'withdraw_no' => 'require|max:50',
            'remark'      => 'require|max:255',
        ],
        'refuse'   => [
            'withdraw_no' => 'require|max:50',
            'remark'      => 'require|max:255',
        ],
    ];
}
