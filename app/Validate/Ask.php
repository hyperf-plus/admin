<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    问答验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/30
 */

namespace App\Validate;


class Ask  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'answer'      => 'max:200',
        'ask_id'      => 'integer|gt:0',
        'ask_type'    => 'require|in:0,1,2,3',
        'title'       => 'require|max:120',
        'ask'         => 'require|max:200',
        'account'     => 'max:80',
        'status'      => 'in:0,1',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:ask_id,ask_type,title,status,create_time,username,nickname',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'answer'      => '回答内容',
        'ask_id'      => '问答编号',
        'ask_type'    => '提问类型',
        'title'       => '提问主题',
        'ask'         => '提问内容',
        'account'     => '账号或昵称',
        'status'      => '是否回答',
        'page_no'     => '页码',
        'page_size'   => '每页数量',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'del'      => [
            'ask_id' => 'require|integer|gt:0',
        ],
        'reply'    => [
            'ask_id' => 'require|integer|gt:0',
            'answer' => 'require|max:200',
        ],
        'continue' => [
            'ask_id' => 'require|integer|gt:0',
            'ask',
        ],
        'item'     => [
            'ask_id' => 'require|integer|gt:0',
        ],
        'list'     => [
            'ask_type' => 'in:0,1,2,3',
            'status',
            'account',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
