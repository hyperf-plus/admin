<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用安装包验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/9
 */

namespace App\Validate;


class AppInstall  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'app_install_id' => 'integer|gt:0',
        'user_agent'     => 'require|max:64',
        'name'           => 'require|max:32',
        'ver'            => 'require|max:16|regex:^\d+(\.\d+){0,3}$',
        'url'            => 'require|max:255',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:app_install_id,name,count,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'app_install_id' => '应用安装包编号',
        'user_agent'     => '系统标识',
        'name'           => '应用安装包名称',
        'ver'            => '应用安装包版本号',
        'url'            => '应用安装包协议地址',
        'page_no'        => '页码',
        'page_size'      => '每页数量',
        'order_type'     => '排序方式',
        'order_field'    => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'     => [
            'app_install_id' => 'require|integer|gt:0',
            'user_agent',
            'name',
            'ver',
            'url',
        ],
        'item'    => [
            'app_install_id' => 'require|integer|gt:0',
        ],
        'del'     => [
            'app_install_id' => 'require|arrayHasOnlyInts',
        ],
        'list'    => [
            'user_agent' => 'max:64',
            'name'       => 'max:32',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'updated' => [
            'user_agent',
            'ver',
        ],
    ];
}
