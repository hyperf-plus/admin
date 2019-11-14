<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    友情链接验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/27
 */

namespace App\Validate;


class FriendLink  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'friend_link_id' => 'integer|gt:0',
        'name'           => 'require|max:50',
        'url'            => 'require|max:255',
        'logo'           => 'max:512',
        'target'         => 'in:_self,_blank',
        'sort'           => 'integer|between:0,255',
        'status'         => 'in:0,1',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:friend_link_id,name,sort,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'friend_link_id' => '友情链接编号',
        'name'           => '友情链接名称',
        'url'            => '友情链接Url',
        'logo'           => '友情链接Logo',
        'target'         => '打开方式',
        'sort'           => '友情链接排序值',
        'status'         => '友情链接状态',
        'order_type'     => '排序方式',
        'order_field'    => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'friend_link_id' => 'require|integer|gt:0',
            'name',
            'url',
            'logo',
            'target',
            'sort',
            'status',
        ],
        'del'    => [
            'friend_link_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'friend_link_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'name' => 'max:50',
            'status',
            'order_type',
            'order_field',
        ],
        'status' => [
            'friend_link_id' => 'require|arrayHasOnlyInts',
            'status'         => 'require|in:0,1',
        ],
        'sort'   => [
            'friend_link_id' => 'require|integer|gt:0',
            'sort'           => 'require|integer|between:0,255',
        ],
        'index'  => [
            'friend_link_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
