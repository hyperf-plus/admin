<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    系统配置验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/2/23
 */

namespace App\Validate;


class Setting  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'data'   => 'require|array',
        'code'   => 'require',
        'value'  => 'max:65535',
        'name'   => 'max:255',
        'model'  => 'max:255',
        'module' => 'max:255',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'code'   => '键名',
        'value'  => '键值',
        'module' => '模块',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'get'         => [
            'code'   => 'max:30',
            'module' => 'require|in:delivery_dist,payment,delivery,system_shopping,service,system_info,upload',
        ],
        'rule'        => [
            'data',
        ],
        'float'       => [
            'value' => 'require|float|regex:-?\d+(\.\d{1,2})?$',
        ],
        'integer'     => [
            'value' => 'require|integer|egt:0',
        ],
        'array'       => [
            'value' => 'array',
        ],
        'int_array'   => [
            'value' => 'arrayHasOnlyInts:zero',
        ],
        'between'     => [
            'value' => 'require|between:0,100',
        ],
        'status'      => [
            'value' => 'require|in:0,1',
        ],
        'string'      => [
            'value',
        ],
        'default_oss' => [
            'value' => 'require|in:careyshop,aliyun,qiniu',
        ],
    ];
}
