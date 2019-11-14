<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    收藏夹验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/15
 */

namespace App\Validate;


class Collect  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'collect_id'  => 'integer|gt:0',
        'goods_id'    => 'require|integer|gt:0',
        'is_top'      => 'in:0,1',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:collect_id,goods_id,create_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'collect_id'  => '收藏夹编号',
        'goods_id'    => '商品编号',
        'is_top'      => '是否置顶',
        'page_no'     => '页码',
        'page_size'   => '每页数量',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'del'  => [
            'collect_id' => 'require|arrayHasOnlyInts',
        ],
        'top'  => [
            'collect_id' => 'require|arrayHasOnlyInts',
            'is_top'     => 'require|in:0,1',
        ],
        'list' => [
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
