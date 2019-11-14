<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    广告位置验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/29
 */

namespace App\Validate;


class AdsPosition  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'ads_position_id' => 'integer|gt:0',
        'code'            => 'max:16|unique:ads_position,code,0,ads_position_id',
        'platform'        => 'require|integer|between:-128,127',
        'name'            => 'require|max:100',
        'description'     => 'max:255',
        'width'           => 'integer|egt:0',
        'height'          => 'integer|egt:0',
        'content'         => 'min:0',
        'color'           => 'max:10',
        'type'            => 'require|in:0,1',
        'display'         => 'in:0,1,2,3',
        'status'          => 'in:0,1',
        'not_empty'       => 'in:0,1',
        'exclude_id'      => 'integer|gt:0',
        'page_no'         => 'integer|gt:0',
        'page_size'       => 'integer|gt:0',
        'order_type'      => 'in:asc,desc',
        'order_field'     => 'in:ads_position_id,name,description,width,height,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'ads_position_id' => '广告位置编号',
        'code'            => '广告位置编码',
        'platform'        => '广告位置平台',
        'name'            => '广告位置名称',
        'description'     => '广告位置描述',
        'width'           => '广告位置宽度',
        'height'          => '广告位置高度',
        'content'         => '广告位置默认内容',
        'color'           => '广告位置背景色',
        'type'            => '广告位置类型',
        'display'         => '广告位置展示方式',
        'status'          => '广告位置状态',
        'not_empty'       => '是否存在关联广告',
        'exclude_id'      => '广告位置排除Id',
        'page_no'         => '页码',
        'page_size'       => '每页数量',
        'order_type'      => '排序方式',
        'order_field'     => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'ads_position_id' => 'require|integer|gt:0',
            'code'            => 'max:16',
            'platform',
            'name'            => 'require|max:100',
            'description',
            'width',
            'height',
            'content',
            'color',
            'type',
            'display',
            'status',
        ],
        'del'    => [
            'ads_position_id' => 'require|arrayHasOnlyInts',
            'not_empty',
        ],
        'unique' => [
            'code' => 'require|max:16',
            'exclude_id',
        ],
        'item'   => [
            'ads_position_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'name'     => 'max:100',
            'code'     => 'max:16',
            'platform' => 'integer|between:-128,127',
            'type'     => 'in:0,1',
            'display',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'status' => [
            'ads_position_id' => 'require|arrayHasOnlyInts',
            'status'          => 'require|in:0,1',
        ],
        'code'   => [
            'code' => 'require|max:16',
        ],
        'select' => [
            'platform' => 'integer|between:-128,127',
            'type'     => 'in:0,1',
            'display',
            'status',
            'order_type',
            'order_field',
        ],
    ];
}
