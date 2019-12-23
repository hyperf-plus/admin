<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    文章管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/29
 */

namespace App\Validate;


use Mzh\Validate\Validate\Validate;

class ArticleValidation  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'article_id'     => 'integer|gt:0',
        'article_cat_id' => 'require|integer|gt:0',
        'title'          => 'require|max:200',
        'image'          => 'max:512',
        'content'        => 'require',
        'source'         => 'max:60|requireWith:source_url',
        'source_url'     => 'max:255|requireWith:source',
        'keywords'       => 'max:255',
        'description'    => 'max:255',
        'url'            => 'max:255',
        'target'         => 'in:_self,_blank',
        'is_top'         => 'in:0,1',
        'status'         => 'in:0,1',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
        'order_type'     => 'in:asc,desc',
        'order_field'    => 'in:article_id,article_cat_id,title,source,is_top,status,create_time,update_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'article_id'     => '文章编号',
        'article_cat_id' => '文章分类编号',
        'title'          => '文章标题',
        'image'          => '文章封面',
        'content'        => '文章内容',
        'source'         => '文章来源',
        'source_url'     => '来源地址',
        'keywords'       => '文章关键词',
        'description'    => '文章描述',
        'url'            => '外部连接',
        'target'         => '打开方式',
        'is_top'         => '是否置顶',
        'status'         => '文章状态',
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
        'set'    => [
            'article_id' => 'require|integer|gt:0',
            'article_cat_id',
            'title',
            'image',
            'content',
            'source',
            'source_url',
            'keywords',
            'description',
            'url',
            'target',
            'is_top',
            'status',
        ],
        'del'    => [
            'article_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'article_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'article_cat_id' => 'integer|gt:0',
            'title'          => 'max:200',
            'keywords',
            'is_top',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'top'    => [
            'article_id' => 'require|arrayHasOnlyInts',
            'is_top'     => 'require|in:0,1',
        ],
        'status' => [
            'article_id' => 'require|arrayHasOnlyInts',
            'status'     => 'require|in:0,1',
        ],
    ];
}
