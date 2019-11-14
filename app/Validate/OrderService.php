<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    售后服务验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/10/10
 */

namespace App\Validate;


class OrderService  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'order_goods_id' => 'require|integer|gt:0',
        'is_refund_fee'  => 'in:0,1',
        'service_no'     => 'max:50',
        'qty'            => 'integer|gt:0',
        'reason'         => 'max:100',
        'description'    => 'max:255',
        'image'          => 'array',
        'result'         => 'max:100',
        'remark'         => 'max:255',
        'is_return'      => 'in:0,1',
        'address'        => 'max:255',
        'consignee'      => 'max:30',
        'zipcode'        => 'max:20',
        'mobile'         => 'number|length:7,15',
        'logistic_code'  => 'max:50',
        'delivery_id'    => 'integer|gt:0',
        'message'        => 'max:255',
        'refund_fee'     => 'float|regex:-?\d+(\.\d{1,2})?$',
        'goods_status'   => 'in:1,2',
        'order_no'       => 'max:50',
        'account'        => 'max:80',
        'type'           => 'in:0,1,2,3',
        'status'         => 'integer|between:0,6',
        'begin_time'     => 'date|betweenTime|beforeTime:end_time',
        'end_time'       => 'date|betweenTime|afterTime:begin_time',
        'my_service'     => 'in:0,1',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:order_service_id,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'order_goods_id' => '订单商品编号',
        'is_refund_fee'  => '是否获取可退金额',
        'service_no'     => '售后单号',
        'qty'            => '数量',
        'reason'         => '原因',
        'description'    => '说明',
        'image'          => '上传图片',
        'result'         => '处理结果',
        'remark'         => '备注',
        'is_return'      => '是否寄回',
        'address'        => '返件地址',
        'consignee'      => '委托人',
        'zipcode'        => '邮编',
        'mobile'         => '电话',
        'logistic_code'  => '快递单号',
        'delivery_id'    => '配送方式编号',
        'message'        => '留言内容',
        'refund_fee'     => '退款金额',
        'goods_status'   => '货物状态',
        'order_no'       => '订单号',
        'account'        => '账号或昵称',
        'type'           => '售后类型',
        'status'         => '售后状态',
        'begin_time'     => '开始日期',
        'end_time'       => '结束日期',
        'my_service'     => '我接收的售后单',
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
        'remark'         => [
            'service_no' => 'require|max:50',
            'remark',
        ],
        'item'           => [
            'service_no' => 'require|max:50',
        ],
        'list'           => [
            'service_no',
            'order_no',
            'account',
            'type',
            'status',
            'begin_time',
            'end_time',
            'my_service',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'maintain'       => [
            'order_goods_id',
            'qty'    => 'require|integer|gt:0',
            'reason' => 'require|max:100',
            'description',
            'image',
        ],
        'sendback'       => [
            'service_no' => 'require|max:50',
            'is_return'  => 'require|in:0,1',
        ],
        'buyer'          => [
            'service_no'    => 'require|max:50',
            'address'       => 'require|max:255',
            'consignee'     => 'require|max:30',
            'zipcode',
            'mobile'        => 'require|number|length:7,15',
            'logistic_code' => 'require|max:50',
            'delivery_id'   => 'require|integer|gt:0',
        ],
        'logistic'       => [
            'service_no'    => 'require|max:50',
            'logistic_code' => 'require|max:50',
            'delivery_id'   => 'require|integer|gt:0',
        ],
        'message'        => [
            'service_no' => 'require|max:50',
            'message'    => 'require|max:255',
        ],
        'refund'         => [
            'order_goods_id',
            'refund_fee'   => 'require|float|regex:-?\d+(\.\d{1,2})?$',
            'goods_status' => 'require|in:1,2',
            'reason'       => 'require|max:100',
            'description',
            'image',
        ],
        'refund_refunds' => [
            'order_goods_id',
            'refund_fee' => 'require|float|regex:-?\d+(\.\d{1,2})?$',
            'reason'     => 'require|max:100',
            'description',
            'image',
        ],
        'agree'          => [
            'service_no' => 'require|max:50',
        ],
        'refused'        => [
            'service_no' => 'require|max:50',
            'result'     => 'require|max:100',
        ],
        'cancel'         => [
            'service_no' => 'require|max:50',
        ],
        'complete'       => [
            'service_no' => 'require|max:50',
            'result'     => 'max:100',
        ],
    ];
}
