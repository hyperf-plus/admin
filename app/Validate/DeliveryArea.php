<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    配送区域验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/28
 */

namespace App\Validate;


class DeliveryArea  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'delivery_area_id'    => 'integer|gt:0',
        'delivery_id'         => 'require|integer|gt:0',
        'name'                => 'require|max:50',
        'region'              => 'arrayHasOnlyInts',
        'first_weight_price'  => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_weight_price' => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_item_price'    => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_item_price'   => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'first_volume_price'  => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'second_volume_price' => 'require|float|egt:0|regex:^\d+(\.\d{1,2})?$',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'delivery_area_id'    => '配送区域编号',
        'delivery_id'         => '配送方式编号',
        'name'                => '配送区域名称',
        'region'              => '所辖区域',
        'first_weight_price'  => '首重运费',
        'second_weight_price' => '续重运费',
        'first_item_price'    => '首件运费',
        'second_item_price'   => '续件运费',
        'first_volume_price'  => '首体积运费',
        'second_volume_price' => '续体积运费',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'  => [
            'delivery_area_id' => 'require|integer|gt:0',
            'delivery_id',
            'name',
            'region',
            'first_weight_price',
            'second_weight_price',
            'first_item_price',
            'second_item_price',
            'first_volume_price',
            'second_volume_price',
        ],
        'del'  => [
            'delivery_area_id' => 'require|arrayHasOnlyInts',
        ],
        'item' => [
            'delivery_area_id' => 'require|integer|gt:0',
        ],
        'list' => [
            'delivery_id' => 'require|integer|gt:0',
            'name'        => 'max:50',
        ],
    ];
}
