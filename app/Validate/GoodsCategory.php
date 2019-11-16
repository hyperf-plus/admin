<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品分类验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/1
 */

namespace App\Validate;


class GoodsCategory  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_category_id' => 'integer|gt:0',
        'parent_id'         => 'integer|egt:0',
        'name'              => 'require|max:100',
        'name_phonetic'     => 'max:10',
        'alias'             => 'max:50',
        'alias_phonetic'    => 'max:10',
        'category_pic'      => 'max:512',
        'category_ico'      => 'max:50',
        'keywords'          => 'max:255',
        'description'       => 'max:255',
        'category_type'     => 'integer|between:-128,127',
        'sort'              => 'integer|between:0,255',
        'is_navi'           => 'in:0,1',
        'status'            => 'in:0,1',
        'not_empty'         => 'in:0,1',
        'goods_total'       => 'in:0,1',
        'level'             => 'integer|egt:0',
        'is_layer'          => 'in:0,1',
        'is_same_level'     => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_category_id' => '商品分类编号',
        'parent_id'         => '商品分类上级编号',
        'name'              => '商品分类名称',
        'name_phonetic'     => '商品分类名称首拼',
        'alias'             => '商品分类别名',
        'alias_phonetic'    => '商品分类别名首拼',
        'category_pic'      => '商品分类图片',
        'category_ico'      => '商品分类图标',
        'keywords'          => '商品分类关键词',
        'description'       => '商品分类描述',
        'category_type'     => '商品分类类型',
        'sort'              => '商品分类排序值',
        'is_navi'           => '是否显示到导航',
        'status'            => '是否显示',
        'not_empty'         => '存在关联不允许删除',
        'goods_total'       => '是否获取关联商品数',
        'level'             => '商品分类深度',
        'is_layer'          => '是否返回本级分类',
        'is_same_level'     => '是否返回同级分类',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'goods_category_id' => 'require|integer|gt:0',
            'parent_id',
            'name',
            'name_phonetic',
            'alias',
            'alias_phonetic',
            'category_pic',
            'category_ico',
            'keywords',
            'description',
            'category_type',
            'sort',
            'is_navi',
            'status',
        ],
        'del'    => [
            'goods_category_id' => 'require|arrayHasOnlyInts',
            'not_empty',
        ],
        'item'   => [
            'goods_category_id' => 'require|integer|egt:0',
        ],
        'navi'   => [
            'goods_category_id' => 'integer|egt:0',
            'is_same_level',
            'is_layer',
        ],
        'status' => [
            'goods_category_id' => 'require|arrayHasOnlyInts',
            'status'            => 'require|in:0,1',
        ],
        'list'   => [
            'goods_category_id' => 'integer|egt:0',
            'level',
            'goods_total',
            'is_layer',
        ],
        'son'    => [
            'goods_category_id' => 'require|arrayHasOnlyInts',
            'level',
            'goods_total',
            'is_layer',
        ],
        'sort'   => [
            'goods_category_id' => 'require|integer|gt:0',
            'sort'              => 'require|integer|between:0,255',
        ],
        'index'  => [
            'goods_category_id' => 'require|arrayHasOnlyInts',
        ],
        'nac'    => [
            'goods_category_id' => 'require|arrayHasOnlyInts',
            'is_navi'           => 'require|in:0,1',
        ],
    ];
}
