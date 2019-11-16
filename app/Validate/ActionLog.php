<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    操作日志验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/10/24
 */

namespace App\Validate;


class ActionLog  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'action_log_id' => 'integer|gt:0',
        'client_type'   => 'in:-1,0,1',
        'username'      => 'max:80',
        'path'          => 'max:255',
        'status'        => 'in:0,1',
        'begin_time'    => 'date|betweenTime|beforeTime:end_time',
        'end_time'      => 'date|betweenTime|afterTime:begin_time',
        'page_no'       => 'integer|gt:0',
        'page_size'     => 'integer|gt:0',
        'order_type'    => 'in:asc,desc',
        'order_field'   => 'in:action_log_id,client_type,username,path,status,create_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'action_log_id' => '操作日志编号',
        'client_type'   => '账号类型',
        'username'      => '账号',
        'path'          => '访问路径',
        'status'        => '执行状态',
        'begin_time'    => '开始日期',
        'end_time'      => '结束日期',
        'page_no'       => '页码',
        'page_size'     => '每页数量',
        'order_type'    => '排序方式',
        'order_field'   => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'item' => [
            'action_log_id' => 'require|integer|gt:0',
        ],
    ];
}
