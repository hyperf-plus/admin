<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品咨询验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/10
 */

namespace App\Validate;


class GoodsConsult  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_consult_id' => 'integer|gt:0',
        'parent_id'        => 'integer|egt:0',
        'goods_id'         => 'require|integer|gt:0',
        'is_anon'          => 'in:0,1',
        'type'             => 'require|integer|between:-128,127',
        'content'          => 'require|max:200',
        'is_show'          => 'in:0,1',
        'status'           => 'in:0,1',
        'is_answer'        => 'in:0,1',
        'account'          => 'max:60',
        'page_no'          => 'integer|gt:0',
        'page_size'        => 'integer|gt:0',
        'order_type'       => 'in:asc,desc',
        'order_field'      => 'in:goods_consult_id,type,content,is_show,is_anon,status,create_time,username,nickname',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_consult_id' => '商品咨询编号',
        'parent_id'        => '商品咨询父Id',
        'goods_id'         => '商品编号',
        'is_anon'          => '是否匿名',
        'type'             => '商品咨询类型',
        'content'          => '内容',
        'is_show'          => '是否显示',
        'status'           => '是否回复',
        'is_answer'        => '是否显示完整问答列表',
        'account'          => '账号或昵称',
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
        'del'   => [
            'goods_consult_id' => 'require|arrayHasOnlyInts',
        ],
        'show'  => [
            'goods_consult_id' => 'require|arrayHasOnlyInts',
            'is_show'          => 'require|in:0,1',
        ],
        'reply' => [
            'goods_consult_id' => 'require|integer|gt:0',
            'content',
        ],
        'item'  => [
            'goods_consult_id' => 'require|integer|gt:0',
        ],
        'list'  => [
            'goods_id' => 'integer|egt:0',
            'content'  => 'max:200',
            'is_show',
            'type'     => 'integer|between:-128,127',
            'status',
            'is_answer',
            'account',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
