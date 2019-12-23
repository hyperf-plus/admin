<?php
declare(strict_types=1);

namespace App\Model;


class Article extends Model
{
    protected $table = 'article';
    protected $primaryKey = 'article_id';
    public $timestamps = true;

    protected $fillable = [
        'article_id',
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
    ];
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'article_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'article_id' => 'integer',
        'article_cat_id' => 'integer',
        'is_top' => 'integer',
        'status' => 'integer',
        'page_views' => 'integer',
    ];

    /**
     * hasOne cs_article_cat
     * @access public
     * @return mixed
     */
    public function getArticleCat()
    {
        return $this->hasOne(ArticleCat::class, 'article_cat_id', 'article_cat_id')->addSelect(['article_cat_id', 'cat_name', 'cat_type']);
    }
}
