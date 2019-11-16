<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/11/27
 */

namespace App\Validate;


class Message  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'message_id'  => 'integer|gt:0',
        'type'        => 'require|integer|between:0,9',
        'member'      => 'require|in:1,2',
        'title'       => 'require|max:200',
        'content'     => 'require',
        'url'         => 'max:255',
        'target'      => 'in:_self,_blank',
        'is_top'      => 'in:0,1',
        'is_read'     => 'in:0,1',
        'status'      => 'in:0,1',
        'is_unread'   => 'in:0,1',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:message_id,type,page_views,is_top,status,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'message_id'  => '消息编号',
        'type'        => '消息类型',
        'member'      => '消息成员组',
        'title'       => '消息标题',
        'content'     => '消息内容',
        'url'         => '外部链接',
        'target'      => '打开方式',
        'is_top'      => '是否置顶',
        'is_read'     => '是否已读',
        'status'      => '消息状态',
        'is_unread'   => '获取未读数',
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
        'set'    => [
            'message_id' => 'require|integer|gt:0',
            'type',
            'title',
            'content',
            'url',
            'target',
            'is_top',
            'status',
        ],
        'item'   => [
            'message_id' => 'require|integer|gt:0',
        ],
        'del'    => [
            'message_id' => 'require|arrayHasOnlyInts',
        ],
        'status' => [
            'message_id' => 'require|arrayHasOnlyInts',
        ],
        'list'   => [
            'type'   => 'integer|between:0,9',
            'member' => 'in:1,2',
            'title'  => 'max:200',
            'is_top',
            'is_read',
            'status',
            'is_unread',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'unread' => [
            'type' => 'integer|between:0,9',
        ],
        'user'   => [
            'message_id' => 'require|arrayHasOnlyInts',
            'type'       => 'integer|between:0,9',
        ],
    ];
}
