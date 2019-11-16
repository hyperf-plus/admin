<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/7
 */

namespace App\Validate;


class GoodsAttribute  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_attribute_id' => 'integer|gt:0',
        'parent_id'          => 'require|integer|gt:0',
        'attr_name'          => 'require|max:60',
        'description'        => 'max:255',
        'icon'               => 'max:512',
        'goods_type_id'      => 'require|integer|gt:0',
        'attr_index'         => 'in:0,1,2',
        'attr_input_type'    => 'require|in:0,1,2',
        'attr_values'        => 'requireIf:attr_input_type,1|requireIf:attr_input_type,2|arrayHasOnlyStrings',
        'is_important'       => 'in:0,1',
        'sort'               => 'integer|between:0,255',
        'attribute_all'      => 'in:0,1',
        'page_no'            => 'integer|gt:0',
        'page_size'          => 'integer|gt:0',
        'order_type'         => 'in:asc,desc',
        'order_field'        => 'in:goods_attribute_id,goods_type_id,attr_name,attr_index,sort',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_attribute_id' => '商品属性编号',
        'parent_id'          => '商品属性主体',
        'attr_name'          => '商品属性名称',
        'description'        => '商品属性描述',
        'icon'               => '商品属性图标(图片)',
        'goods_type_id'      => '所属商品模型编号',
        'attr_index'         => '商品属性检索',
        'attr_input_type'    => '商品属性录入方式',
        'attr_values'        => '商品属性可选值',
        'is_important'       => '是否属于核心属性',
        'sort'               => '商品属性排序值',
        'attribute_all'      => '获取所有(包括已删除)',
        'page_no'            => '页码',
        'page_size'          => '每页数量',
        'order_type'         => '排序方式',
        'order_field'        => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'body'      => [
            'attr_name',
            'description',
            'icon',
            'goods_type_id',
            'sort',
        ],
        'bodyset'   => [
            'goods_attribute_id' => 'require|integer|gt:0',
            'goods_type_id',
            'attr_name',
            'description',
            'icon',
            'sort',
        ],
        'set'       => [
            'goods_attribute_id' => 'require|integer|gt:0',
            'parent_id',
            'attr_name',
            'description',
            'icon',
            'goods_type_id',
            'attr_index',
            'attr_input_type'    => 'requireWith:attr_values|in:0,1,2',
            'attr_values',
            'is_important',
            'sort',
        ],
        'item'      => [
            'goods_attribute_id' => 'require|integer|gt:0',
        ],
        'page'      => [
            'goods_type_id' => 'integer|gt:0',
            'attribute_all',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'list'      => [
            'goods_type_id',
            'attribute_all',
        ],
        'key'       => [
            'goods_attribute_id' => 'require|arrayHasOnlyInts',
            'attr_index'         => 'require|in:0,1,2',
        ],
        'important' => [
            'goods_attribute_id' => 'require|arrayHasOnlyInts',
            'is_important'       => 'require|in:0,1',
        ],
        'sort'      => [
            'goods_attribute_id' => 'require|integer|gt:0',
            'sort'               => 'require|integer|between:0,255',
        ],
        'index'     => [
            'goods_attribute_id' => 'require|arrayHasOnlyInts',
        ],
        'del'       => [
            'goods_attribute_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
