<?php
declare(strict_types=1);

namespace App\Model;

class ArticleCat extends Model
{
    protected $table = 'article_cat';
    protected $primaryKey = 'article_cat_id';
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'article_cat_id',
    ];
    protected $fillable = ['parent_id', 'cat_name', 'cat_type', 'keywords', 'description', 'sort', 'is_navi'];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'article_cat_id' => 'integer',
        'parent_id' => 'integer',
        'cat_type' => 'integer',
        'sort' => 'integer',
        'is_navi' => 'integer',
    ];

}
