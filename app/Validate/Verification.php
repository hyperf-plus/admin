<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    验证码验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/20
 */

namespace App\Validate;


class Verification  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'mobile'   => 'number|length:7,15',
        'email'    => 'email|max:60',
        'code'     => 'integer|max:6',
        'number'   => 'max:60',
        'is_check' => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'mobile'   => '手机号',
        'email'    => '邮箱地址',
        'code'     => '验证码',
        'number'   => '手机号或邮箱地址',
        'is_check' => '是否验证通过',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'sms'       => [
            'mobile' => 'require|number|length:7,15',
        ],
        'email'     => [
            'email' => 'require|email|max:60',
        ],
        'ver_sms'   => [
            'mobile' => 'require|number|length:7,15',
            'code'   => 'require|integer|max:6',
        ],
        'ver_email' => [
            'email' => 'require|email|max:60',
            'code'  => 'require|integer|max:6',
        ],
        'use'       => [
            'number' => 'require|max:60',
            'is_check',
        ],
    ];
}
