<?php
/**
 * 专题验证器
 */
namespace App\Validate;

use Mzh\Validate\Validate\Validate;

class TopicValidation  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'topic_id'    => 'integer|gt:0',
        'title'       => 'require|max:200',
        'alias'       => 'max:100',
        'content'     => 'require',
        'keywords'    => 'max:255',
        'description' => 'max:255',
        'status'      => 'in:0,1',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'in:asc,desc',
        'order_field' => 'in:topic_id,title,alias,status,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'topic_id'    => '专题编号',
        'title'       => '专题标题',
        'alias'       => '专题别名',
        'content'     => '专题内容',
        'keywords'    => '专题关键词',
        'description' => '专题描述',
        'status'      => '专题是否显示',
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
            'topic_id' => 'require|integer|gt:0',
            'title',
            'alias',
            'content',
            'keywords',
            'description',
            'status',
        ],
        'del'    => [
            'topic_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'topic_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'title' => 'max:200',
            'alias',
            'keywords',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'status' => [
            'topic_id' => 'require|arrayHasOnlyInts',
            'status'   => 'require|in:0,1',
        ],
    ];
}
