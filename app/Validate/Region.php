<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    区域模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/27
 */

namespace App\Validate;


class Region  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'region_id'   => 'integer|gt:0',
        'parent_id'   => 'integer|egt:0',
        'region_name' => 'require|max:120',
        'sort'        => 'integer|between:0,255',
        'region_all'  => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'region_id'   => '区域编号',
        'parent_id'   => '父区域编号',
        'region_name' => '区域名称',
        'sort'        => '区域排序值',
        'region_all'  => '所有区域(包括已删除)',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'   => [
            'region_id' => 'require|integer|gt:0',
            'region_name',
            'sort',
        ],
        'del'   => [
            'region_id' => 'require|arrayHasOnlyInts',
        ],
        'item'  => [
            'region_id' => 'require|integer|gt:0',
            'region_all',
        ],
        'list'  => [
            'region_id' => 'integer|egt:0',
            'region_all',
        ],
        'sort'  => [
            'region_id' => 'require|integer|gt:0',
            'sort'      => 'require|integer|between:0,255',
        ],
        'index' => [
            'region_id' => 'require|arrayHasOnlyInts',
        ],
        'name'  => [
            'region_id' => 'require|arrayHasOnlyInts:zero',
        ],
    ];
}
