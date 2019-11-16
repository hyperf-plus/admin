<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/29
 */

namespace App\Validate;


class Order  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'order_no'             => 'max:50',
        'source'               => 'require|integer|between:0,9',
        'type'                 => 'require|in:buynow,cart',
        'is_submit'            => 'in:0,1',
        'delivery_id'          => 'integer|egt:0',
        'use_money'            => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'use_integral'         => 'integer|egt:0',
        'coupon_give_id'       => 'integer|gt:0',
        'coupon_exchange_code' => 'max:10',
        'consignee'            => 'max:50',
        'country'              => 'integer|egt:0',
        'province'             => 'integer|gt:0',
        'city'                 => 'integer|gt:0',
        'district'             => 'integer|egt:0',
        'address'              => 'max:255',
        'zipcode'              => 'max:20',
        'tel'                  => 'max:20',
        'mobile'               => 'length:7,15',
        'buyer_remark'         => 'max:255',
        'invoice_type'         => 'in:0,1,2|requireWith:invoice_title', // 0=不需要发票 1=个人 2=企业
        'invoice_title'        => 'max:255|requireIf:invoice_type,2',
        'tax_number'           => 'max:20|requireIf:invoice_type,2',
        'payment_status'       => 'in:0,1',
        'total_amount'         => 'float|regex:-?\d+(\.\d{1,2})?$',
        'sellers_remark'       => 'max:255',
        'is_get_log'           => 'in:0,1',
        'is_recycle'           => 'in:0,1,2',
        'order_goods_id'       => 'arrayHasOnlyInts',
        'logistic_code'        => 'max:50',
        'is_export'            => 'in:0,1',
        'payment_code'         => 'integer|egt:0',
        'is_delete'            => 'in:0,1',
        'begin_time'           => 'date|betweenTime|beforeTime:end_time',
        'end_time'             => 'date|betweenTime|afterTime:begin_time',
        'status'               => 'integer|between:0,7',
        'goods_name'           => 'max:200',
        'keywords'             => 'max:200',
        'use_card'             => 'requireWith:card_number|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'card_number'          => 'requireWith:use_card|length:16',
        'comment_type'         => 'in:comment,addition',
        'account'              => 'max:80',
        'page_no'              => 'integer|gt:0',
        'page_size'            => 'integer|gt:0',
        'order_type'           => 'in:asc,desc',
        'order_field'          => 'in:order_id,payment_time,finished_time,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'order_no'             => '订单号',
        'source'               => '订单来源',
        'type'                 => '立即购买或购物车结算',
        'is_submit'            => '是否提交订单',
        'delivery_id'          => '配送方式编号',
        'use_money'            => '余额支付',
        'use_integral'         => '积分支付',
        'coupon_give_id'       => '优惠劵发放编号',
        'coupon_exchange_code' => '优惠劵兑换码',
        'consignee'            => '收货人姓名',
        'country'              => '收货国家',
        'province'             => '收货省份',
        'city'                 => '收货城市',
        'district'             => '收货县区',
        'address'              => '收货详细地址',
        'zipcode'              => '收货邮编',
        'tel'                  => '收货人电话',
        'mobile'               => '收货人手机号码',
        'buyer_remark'         => '给卖家留言',
        'invoice_type'         => '开票方式',
        'invoice_title'        => '发票抬头',
        'tax_number'           => '纳税人识别号',
        'payment_status'       => '支付状态',
        'total_amount'         => '应付金额',
        'sellers_remark'       => '卖家备注',
        'is_get_log'           => '是否获取订单操作日志',
        'is_recycle'           => '是否放入回收站',
        'order_goods_id'       => '订单商品编号',
        'logistic_code'        => '快递单号',
        'is_export'            => '是否导出',
        'payment_code'         => '支付方式',
        'is_delete'            => '是否查看订单回收站',
        'begin_time'           => '下单开始日期',
        'end_time'             => '下单结束日期',
        'status'               => '订单状态',
        'goods_name'           => '商品名称',
        'keywords'             => '商品名称或订单号',
        'use_card'             => '购物卡支付',
        'card_number'          => '购物卡卡号',
        'comment_type'         => '评价方式',
        'account'              => '账号或昵称',
        'page_no'              => '页码',
        'page_size'            => '每页数量',
        'order_type'           => '排序方式',
        'order_field'          => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'create'       => [
            'delivery_id' => 'require|integer|gt:0',
            'source',
            'use_money',
            'use_integral',
            'coupon_give_id',
            'coupon_exchange_code',
            'consignee'   => 'require|max:50',
            'country',
            'province'    => 'require|integer|gt:0',
            'city'        => 'require|integer|gt:0',
            'district',
            'address'     => 'require|max:255',
            'zipcode',
            'tel',
            'mobile'      => 'require|length:7,15',
            'buyer_remark',
            'invoice_type',
            'invoice_title',
            'tax_number',
            'use_card',
            'card_number',
        ],
        'is_payment'   => [
            'order_no' => 'require|max:50',
        ],
        'change_price' => [
            'order_no' => 'require|max:50',
            'total_amount',
        ],
        'cancel'       => [
            'order_no' => 'require|max:50',
        ],
        'remark'       => [
            'order_no'       => 'require|max:50',
            'sellers_remark' => 'require|max:255',
        ],
        'set'          => [
            'order_no'  => 'require|max:50',
            'consignee' => 'require|max:50',
            'country',
            'province'  => 'require|integer|gt:0',
            'city'      => 'require|integer|gt:0',
            'district',
            'address'   => 'require|max:255',
            'zipcode',
            'tel',
            'mobile'    => 'require|length:7,15',
            'invoice_title',
            'tax_number',
        ],
        'item'         => [
            'order_no' => 'require|max:50',
            'is_get_log',
        ],
        'recycle'      => [
            'order_no'   => 'require|max:50',
            'is_recycle' => 'require|in:0,1,2',
        ],
        'picking'      => [
            'order_no' => 'require|max:50',
        ],
        'delivery'     => [
            'order_no'       => 'require|max:50',
            'order_goods_id' => 'require|arrayHasOnlyInts',
            'logistic_code',
            'delivery_id',
        ],
        'complete'     => [
            'order_no' => 'require|max:50',
        ],
        'list'         => [
            'is_export',
            'keywords',
            'consignee',
            'mobile',
            'payment_code',
            'is_delete',
            'begin_time',
            'end_time',
            'status',
            'account',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'buy_again'    => [
            'order_no' => 'require|max:50',
        ],
        'comment'      => [
            'order_no'     => 'require|max:50',
            'comment_type' => 'require|in:comment,addition',
        ],
        'goods_item'   => [
            'order_goods_id' => 'require|integer|gt:0',
        ],
    ];
}
