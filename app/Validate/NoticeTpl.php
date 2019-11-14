<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统模板验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/18
 */

namespace App\Validate;


class NoticeTpl  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'notice_tpl_id' => 'integer|gt:0',
        'name'          => 'max:30',
        'code'          => 'in:sms,email',
        'type'          => 'integer|between:0,6',
        'sms_code'      => 'max:20',
        'sms_sign'      => 'length:2,12',
        'title'         => 'max:255',
        'template'      => 'min:0',
        'status'        => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'notice_tpl_id' => '通知系统模板编号',
        'name'          => '通知系统模板名称',
        'code'          => '通知系统编码',
        'type'          => '通知类型',
        'sms_code'      => '阿里云短信模板编号',
        'sms_sign'      => '阿里云短信签名',
        'title'         => '通知系统标题',
        'template'      => '通知系统模板',
        'status'        => '模板是否启用',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'item'      => [
            'notice_tpl_id' => 'require|integer|gt:0',
        ],
        'list'      => [
            'code',
        ],
        'set_sms'   => [
            'sms_code' => 'require|max:20',
            'sms_sign' => 'require|length:2,12',
            'template' => 'require',
        ],
        'set_email' => [
            'title'    => 'require|max:255',
            'template' => 'require',
        ],
        'status'    => [
            'notice_tpl_id' => 'require|arrayHasOnlyInts',
            'status'        => 'require|in:0,1',
        ],
    ];
}
