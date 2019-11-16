<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/17
 */

namespace App\Validate;


class Notice  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'code'       => 'in:sms,email',
        'status'     => 'in:0,1',
        'key_id'     => 'max:255',
        'key_secret' => 'max:255',
        'email_host' => 'max:255',
        'email_port' => 'max:255',
        'email_addr' => 'max:255',
        'email_id'   => 'max:255',
        'email_pass' => 'max:255',
        'email_ssl'  => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'code'       => '通知系统编码',
        'status'     => '状态',
        'key_id'     => 'Access Key ID',
        'key_secret' => 'Access Key Secret',
        'email_host' => 'SMTP服务器',
        'email_port' => 'SMTP端口',
        'email_addr' => '发信人邮箱地址',
        'email_id'   => 'SMTP身份验证用户名',
        'email_pass' => 'SMTP身份验证码',
        'email_ssl'  => '是否使用安全链接',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'item'      => [
            'code' => 'require|in:sms,email',
        ],
        'set_sms'   => [
            'key_id'     => 'require|max:255',
            'key_secret' => 'require|max:255',
            'status'     => 'require|in:0,1',
        ],
        'set_email' => [
            'email_host' => 'require|max:255',
            'email_port' => 'require|max:255',
            'email_addr' => 'require|max:255',
            'email_id'   => 'require|max:255',
            'email_pass' => 'require|max:255',
            'email_ssl'  => 'require|in:0,1',
            'status'     => 'require|in:0,1',
        ],
        'status'    => [
            'code'   => 'require|arrayHasOnlyStrings',
            'status' => 'require|in:0,1',
        ],
    ];
}
