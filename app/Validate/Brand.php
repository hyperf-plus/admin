<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    品牌验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/1
 */

namespace App\Validate;


class Brand  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'brand_id'          => 'integer|gt:0',
        'goods_category_id' => 'integer|egt:0',
        'name'              => 'require|max:50',
        'phonetic'          => 'max:10',
        'description'       => 'max:100',
        'logo'              => 'max:512',
        'url'               => 'max:255',
        'target'            => 'in:_self,_blank',
        'sort'              => 'integer|between:0,255',
        'status'            => 'in:0,1',
        'exclude_id'        => 'integer|gt:0',
        'page_no'           => 'integer|gt:0',
        'page_size'         => 'integer|gt:0',
        'order_type'        => 'in:asc,desc',
        'order_field'       => 'in:brand_id,goods_category_id,name,phonetic,description,sort,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'brand_id'          => '品牌编号',
        'goods_category_id' => '商品分类编号',
        'name'              => '品牌名称',
        'phonetic'          => '品牌首拼',
        'description'       => '品牌描述',
        'logo'              => '品牌LOGO',
        'url'               => '品牌链接地址',
        'target'            => '打开方式',
        'sort'              => '品牌排序值',
        'status'            => '品牌是否显示',
        'exclude_id'        => '品牌排除Id',
        'page_no'           => '页码',
        'page_size'         => '每页数量',
        'order_type'        => '排序方式',
        'order_field'       => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'brand_id' => 'require|integer|gt:0',
            'goods_category_id',
            'name'     => 'require|max:50',
            'phonetic',
            'description',
            'logo',
            'url',
            'target',
            'sort',
            'status',
        ],
        'del'    => [
            'brand_id' => 'require|arrayHasOnlyInts',
        ],
        'status' => [
            'brand_id' => 'require|arrayHasOnlyInts',
            'status'   => 'require|in:0,1',
        ],
        'unique' => [
            'name' => 'require|max:50',
            'goods_category_id',
            'exclude_id',
        ],
        'item'   => [
            'brand_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'goods_category_id' => 'integer|egt:0',
            'name'              => 'max:50',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'select' => [
            'goods_category_id' => 'arrayHasOnlyInts:zero',
            'order_type',
            'order_field',
        ],
        'sort'   => [
            'brand_id' => 'require|integer|gt:0',
            'sort'     => 'require|integer|between:0,255',
        ],
        'index'  => [
            'brand_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
