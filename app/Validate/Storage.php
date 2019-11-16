<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/1/10
 */

namespace App\Validate;


class Storage  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'storage_id'  => 'integer|gt:0',
        'parent_id'   => 'integer|egt:0',
        'name'        => 'require|max:255',
        'mime'        => 'max:100',
        'ext'         => 'max:6',
        'size'        => 'integer|egt:0',
        'hash'        => 'max:40',
        'path'        => 'max:255',
        'url'         => 'max:255',
        'protocol'    => 'require|max:10',
        'type'        => 'in:0,1,2',
        'cover'       => 'max:255',
        'sort'        => 'integer|between:0,255',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:storage_id,name,type,create_time,update_time',
        'is_layer'    => 'in:0,1',
        'is_default'  => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'storage_id'  => '资源编号',
        'parent_id'   => '所属资源编号',
        'name'        => '资源名称',
        'mime'        => '资源Mime',
        'ext'         => '资源后缀',
        'size'        => '资源大小',
        'hash'        => '资源Hash值',
        'path'        => '资源内部路径',
        'url'         => '资源外链地址',
        'protocol'    => '资源协议',
        'type'        => '资源类型',
        'cover'       => '封面',
        'sort'        => '排序值',
        'page_no'     => '页码',
        'page_size'   => '每页数量',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
        'is_layer'    => '是否返回本级',
        'is_default'  => '是否设为默认',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'add_directory'  => [
            'name',
            'parent_id',
            'sort',
        ],
        'set_directory'  => [
            'storage_id' => 'require|integer|gt:0',
            'name',
            'sort',
        ],
        'list_directory' => [
            'order_type',
            'order_field',
        ],
        'item'           => [
            'storage_id' => 'require|integer|gt:0',
        ],
        'list'           => [
            'storage_id' => 'integer|egt:0',
            'name'       => 'max:255',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'rename'         => [
            'storage_id' => 'require|integer|gt:0',
            'name',
        ],
        'move'           => [
            'storage_id' => 'require|arrayHasOnlyInts',
            'parent_id'  => 'require|integer|egt:0',
        ],
        'del'            => [
            'storage_id' => 'require|arrayHasOnlyInts',
        ],
        'thumb'          => [
            'storage_id' => 'require|integer|gt:0',
        ],
        'replace'        => [
            'storage_id' => 'require|integer|gt:0',
        ],
        'navi'           => [
            'storage_id' => 'integer|egt:0',
            'is_layer',
        ],
        'default'        => [
            'storage_id' => 'require|integer|gt:0',
            'is_default' => 'require|in:0,1',
        ],
        'cover'          => [
            'storage_id' => 'require|integer|egt:0',
        ],
    ];
}
