<?php
declare(strict_types=1);
namespace App\Validate;

use Mzh\Validate\Validate\Validate;

class ArticleCatValidation extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'article_cat_id' => 'integer|gt:0',
        'parent_id'      => 'integer|egt:0',
        'cat_name'       => 'require|max:100',
        'cat_type'       => 'integer|between:-128,127',
        'keywords'       => 'max:255',
        'description'    => 'max:255',
        'sort'           => 'integer|between:0,255',
        'is_navi'        => 'in:0,1',
        'not_empty'      => 'in:0,1',
        'level'          => 'integer|egt:0',
        'is_layer'       => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'article_cat_id' => '文章分类编号',
        'parent_id'      => '文章分类上级编号',
        'cat_name'       => '文章分类名称',
        'cat_type'       => '文章分类类型',
        'keywords'       => '文章分类关键词',
        'description'    => '文章分类描述',
        'sort'           => '文章分类排序值',
        'is_navi'        => '是否显示到导航',
        'not_empty'      => '存在关联不允许删除',
        'level'          => '文章分类深度',
        'is_layer'       => '是否返回本级分类',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'   => [
            'article_cat_id' => 'require|integer|gt:0',
            'parent_id',
            'cat_name',
            'cat_type',
            'keywords',
            'description',
            'sort',
            'is_navi',
        ],
        'del'   => [
            'article_cat_id' => 'require|arrayHasOnlyInts',
            'not_empty',
        ],
        'item'  => [
            'article_cat_id' => 'require|integer|gt:0',
        ],
        'list'  => [
            'article_cat_id' => 'integer|egt:0',
            'level',
            'is_navi',
            'is_layer',
        ],
        'navi'  => [
            'article_cat_id' => 'integer|egt:0',
            'is_layer',
        ],
        'sort'  => [
            'article_cat_id' => 'require|integer|gt:0',
            'sort'           => 'require|integer|between:0,255',
        ],
        'index' => [
            'article_cat_id' => 'require|arrayHasOnlyInts',
        ],
        'nac'   => [
            'article_cat_id' => 'require|arrayHasOnlyInts',
            'is_navi'        => 'require|in:0,1',
        ],
    ];
}
