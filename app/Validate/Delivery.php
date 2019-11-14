<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    配送方式验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/27
 */

namespace App\Validate;


class Delivery  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'delivery_id'         => 'integer|gt:0',
        'delivery_item_id'    => 'require|integer|gt:0|unique:delivery,delivery_item_id,0,delivery_id',
        'content'             => 'max:150',
        'first_weight'        => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_weight_price'  => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_weight'       => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_weight_price' => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_item'          => 'require|integer|between:0,255',
        'first_item_price'    => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_item'         => 'require|integer|between:0,255',
        'second_item_price'   => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_volume'        => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_volume_price'  => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_volume'       => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_volume_price' => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'sort'                => 'integer|between:0,255',
        'status'              => 'in:0,1',
        'region_id'           => 'integer|gt:0',
        'weight_total'        => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'item_total'          => 'integer|egt:0',
        'volume_total'        => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'exclude_id'          => 'integer|gt:0',
        'name'                => 'max:50',
        'order_type'          => 'in:asc,desc',
        'order_field'         => 'in:delivery_id,name,content,sort,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'delivery_id'         => '配送方式编号',
        'delivery_item_id'    => '快递公司编号',
        'content'             => '配送方式描述',
        'first_weight'        => '首重',
        'first_weight_price'  => '续重费运',
        'second_weight'       => '续重',
        'second_weight_price' => '续重运费',
        'first_item'          => '首件',
        'first_item_price'    => '首件运费',
        'second_item'         => '续件',
        'second_item_price'   => '续件运费',
        'first_volume'        => '首体积量',
        'first_volume_price'  => '首体积运费',
        'second_volume'       => '续体积量',
        'second_volume_price' => '续体积运费',
        'sort'                => '配送方式排序值',
        'status'              => '配送方式状态',
        'region_id'           => '区域编号',
        'weight_total'        => '重量合计',
        'item_total'          => '计件合计',
        'volume_total'        => '体积量合计',
        'exclude_id'          => '配送方式排除Id',
        'name'                => '快递公司名称',
        'order_type'          => '排序方式',
        'order_field'         => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'     => [
            'delivery_id'      => 'require|integer|gt:0',
            'delivery_item_id' => 'require|integer|gt:0',
            'content',
            'first_weight',
            'first_weight_price',
            'second_weight',
            'second_weight_price',
            'first_item',
            'first_item_price',
            'second_item',
            'second_item_price',
            'first_volume',
            'first_volume_price',
            'second_volume',
            'second_volume_price',
            'sort',
            'status',
        ],
        'del'     => [
            'delivery_id' => 'require|arrayHasOnlyInts',
        ],
        'item'    => [
            'delivery_id' => 'require|integer|gt:0',
        ],
        'list'    => [
            'name',
            'status',
            'order_type',
            'order_field',
        ],
        'freight' => [
            'delivery_id' => 'require|integer|gt:0',
            'region_id'   => 'require|integer|gt:0',
            'weight_total',
            'item_total',
            'volume_total',
        ],
        'status'  => [
            'delivery_id' => 'require|arrayHasOnlyInts',
            'status'      => 'require|in:0,1',
        ],
        'unique'  => [
            'delivery_item_id' => 'require|integer|gt:0',
            'exclude_id',
        ],
        'sort'    => [
            'delivery_id' => 'require|integer|gt:0',
            'sort'        => 'require|integer|between:0,255',
        ],
        'index'   => [
            'delivery_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
