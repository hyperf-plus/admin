<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品评价验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/11
 */

namespace App\Validate;


class GoodsComment  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_comment_id' => 'integer|gt:0',
        'goods_id'         => 'require|integer|gt:0',
        'order_goods_id'   => 'require|integer|gt:0',
        'parent_id'        => 'integer|egt:0',
        'order_no'         => 'require|max:50',
        'is_anon'          => 'in:0,1',
        'type'             => 'integer|between:0,3',
        'content'          => 'max:200',
        'image'            => 'array',
        'score'            => 'require|integer|between:1,5',
        'is_show'          => 'in:0,1',
        'is_top'           => 'in:0,1',
        'status'           => 'in:0,1',
        'account'          => 'max:60',
        'is_image'         => 'in:0,1',
        'is_append'        => 'in:0,1',
        'goods_spec'       => 'arrayHasOnlyInts',
        'page_no'          => 'integer|gt:0',
        'page_size'        => 'integer|gt:0',
        'order_type'       => 'in:asc,desc',
        'order_field'      => 'in:goods_comment_id,is_image,score,is_show,is_top,status,create_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_comment_id' => '商品评价编号',
        'goods_id'         => '商品编号',
        'order_goods_id'   => '订单商品编号',
        'parent_id'        => '商品评价上级编号',
        'order_no'         => '订单号',
        'is_anon'          => '是否匿名',
        'type'             => '商品评价类型',
        'content'          => '商品评价内容',
        'image'            => '商品评价图片',
        'score'            => '商品评价得分',
        'is_show'          => '是否显示',
        'is_top'           => '是否置顶',
        'status'           => '是否已读',
        'account'          => '账号或昵称',
        'is_image'         => '是否有图',
        'is_append'        => '是否有追评',
        'goods_spec'       => '商品规格',
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
        'addition' => [
            'order_goods_id',
            'order_no',
            'content' => 'require|max:200',
            'image',
        ],
        'reply'    => [
            'goods_comment_id' => 'require|integer|gt:0',
            'content'          => 'require|max:200',
            'image',
        ],
        'del'      => [
            'goods_comment_id' => 'require|integer|gt:0',
        ],
        'praise'   => [
            'goods_comment_id' => 'require|integer|gt:0',
        ],
        'score'    => [
            'goods_id',
        ],
        'show'     => [
            'goods_comment_id' => 'require|arrayHasOnlyInts',
            'is_show'          => 'require|in:0,1',
        ],
        'top'      => [
            'goods_comment_id' => 'require|arrayHasOnlyInts',
            'is_top'           => 'require|in:0,1',
        ],
        'status'   => [
            'goods_comment_id' => 'require|arrayHasOnlyInts',
            'status'           => 'require|in:0,1',
        ],
        'count'    => [
            'goods_id',
        ],
        'item'     => [
            'goods_comment_id' => 'require|integer|gt:0',
        ],
        'list'     => [
            'goods_id' => 'integer|gt:0',
            'order_no' => 'max:50',
            'account',
            'content',
            'score'    => 'integer|between:0,2',
            'is_show',
            'is_top',
            'is_image',
            'is_append',
            'status',
            'goods_spec',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
