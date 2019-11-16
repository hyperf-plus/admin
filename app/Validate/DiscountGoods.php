<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    折扣商品验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/31
 */

namespace App\Validate;


class DiscountGoods  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'discount_id'       => 'integer|gt:0',
        'goods_id'          => 'require|integer|gt:0',
        'discount'          => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'discount_id'       => '折扣编号',
        'goods_id'          => '折扣商品编号',
        'discount'          => '折扣商品折扣额',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'info' => [
            'goods_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
