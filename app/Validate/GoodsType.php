<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品模型验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/7
 */

namespace App\Validate;


class GoodsType  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_type_id' => 'integer|gt:0',
        'type_name'     => 'require|max:60|unique:goods_type,type_name,0,goods_type_id',
        'exclude_id'    => 'integer|gt:0',
        'page_no'       => 'integer|gt:0',
        'page_size'     => 'integer|gt:0',
        'order_type'    => 'in:asc,desc',
        'order_field'   => 'in:goods_type_id,type_name',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_type_id' => '商品模型编号',
        'type_name'     => '商品模型名称',
        'exclude_id'    => '商品模型排除Id',
        'page_no'       => '页码',
        'page_size'     => '每页数量',
        'order_type'    => '排序方式',
        'order_field'   => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'goods_type_id' => 'require|integer|gt:0',
            'type_name'     => 'require|max:60',
        ],
        'unique' => [
            'type_name' => 'require|max:60',
            'exclude_id',
        ],
        'item'   => [
            'goods_type_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'type_name' => 'max:60',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'del'    => [
            'goods_type_id' => 'require|arrayHasOnlyInts',
        ],
        'select' => [
            'order_type',
            'order_field',
        ],
    ];
}
